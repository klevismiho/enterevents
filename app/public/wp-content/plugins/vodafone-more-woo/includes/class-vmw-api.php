<?php
defined('ABSPATH') || exit;

/**
 * VMW_API
 * Wraps every Vodafone More REST v2.0 command.
 * Tokens are persisted in WordPress options so they survive page loads.
 */
class VMW_API
{

  // ------------------------------------------------------------------ config
  const ENDPOINTS = [
    'development' => 'https://mobileapidev.vodafonecoupons.al/v.2/',
    'production'  => 'https://mobileapi.vodafonecoupons.al/v.2/',
  ];

  const TYPE_ID = 3;

  // ------------------------------------------------------------------ singleton
  private static ?VMW_API $instance = null;

  public static function instance(): self
  {
    if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  // ------------------------------------------------------------------ helpers
  private function env(): string
  {
    return VMW_Settings::get_env();
  }

  private function base_url(): string
  {
    return self::ENDPOINTS[$this->env()] ?? self::ENDPOINTS['production'];
  }

  private function credentials(): array
  {
    $env      = $this->env();
    $short    = $env === 'production' ? 'prod' : 'dev';
    $username = VMW_Settings::get_username($short);
    $password = VMW_Settings::get_password($short);

    if (empty($username) || empty($password)) {
      return ['error' => "API credentials not set. Please go to Settings → Vodafone More."];
    }
    return ['username' => $username, 'password' => $password];
  }

  // ------------------------------------------------------------------ token storage
  private function get_session(): array
  {
    return get_option('vmw_api_session_' . $this->env(), []);
  }

  private function save_session(array $data): void
  {
    update_option('vmw_api_session_' . $this->env(), $data, false);
  }

  private function clear_session(): void
  {
    delete_option('vmw_api_session_' . $this->env());
  }

  private function token_valid(): bool
  {
    $session = $this->get_session();
    if (empty($session['token']) || empty($session['token_expire_timestamp'])) {
      return false;
    }
    // Treat token as expired 60 seconds early to avoid edge cases
    return time() < ((int) $session['token_expire_timestamp'] - 60);
  }

  // ------------------------------------------------------------------ HTTP
  private function post(array $body): array
  {
    $response = wp_remote_post($this->base_url(), [
      'timeout' => 3,
      'body'    => $body,
    ]);

    if (is_wp_error($response)) {
      return [
        'status_code'    => 0,
        'status_message' => $response->get_error_message(),
        'data'           => [],
        'errors'         => [],
      ];
    }

    $decoded = json_decode(wp_remote_retrieve_body($response), true);
    if (! is_array($decoded)) {
      return [
        'status_code'    => 0,
        'status_message' => 'Invalid JSON response from Vodafone API.',
        'data'           => [],
        'errors'         => [],
      ];
    }
    return $decoded;
  }

  // ------------------------------------------------------------------ 1. login
  public function login(): array
  {
    $creds = $this->credentials();
    if (isset($creds['error'])) {
      return ['status_code' => 0, 'status_message' => $creds['error'], 'data' => [], 'errors' => []];
    }

    $result = $this->post([
      'action'             => 'login',
      'type_id'            => self::TYPE_ID,
      'username'           => $creds['username'],
      'password'           => $creds['password'],
      'recaptcha_response' => '-',
    ]);

    if (200 === (int) ($result['status_code'] ?? 0)) {
      $this->save_session([
        'user_id'                => $result['data']['user_id'],
        'token'                  => $result['data']['token'],
        'refresh_token'          => $result['data']['refresh_token'],
        'token_expire_timestamp' => $result['data']['token_expire_timestamp'],
      ]);
    }
    return $result;
  }

  // ------------------------------------------------------------------ 2. refreshToken
  public function refresh_token(): array
  {
    $session = $this->get_session();
    if (empty($session['token'])) {
      return $this->login();
    }

    $result = $this->post([
      'action'        => 'refreshToken',
      'type_id'       => self::TYPE_ID,
      'user_id'       => $session['user_id'],
      'token'         => $session['token'],
      'refresh_token' => $session['refresh_token'],
    ]);

    if (200 === (int) ($result['status_code'] ?? 0)) {
      $this->save_session([
        'user_id'                => $result['data']['user_id'],
        'token'                  => $result['data']['token'],
        'refresh_token'          => $result['data']['refresh_token'],
        'token_expire_timestamp' => $session['token_expire_timestamp'] ?? (time() + 3600),
      ]);
    }
    return $result;
  }

  // ------------------------------------------------------------------ ensure authenticated
  private function ensure_auth(): array|\WP_Error
  {
    if (! $this->token_valid()) {
      $session = $this->get_session();
      if (! empty($session['refresh_token'])) {
        $r = $this->refresh_token();
      } else {
        $r = $this->login();
      }
      if (200 !== (int) ($r['status_code'] ?? 0)) {
        return new \WP_Error('vmw_auth', $r['status_message'] ?? 'Authentication failed.');
      }
    }
    return $this->get_session();
  }

  private function authenticated_post(array $body): array
  {
    $session = $this->ensure_auth();
    if (is_wp_error($session)) {
      return ['status_code' => 401, 'status_message' => $session->get_error_message(), 'data' => [], 'errors' => []];
    }

    $body['user_id'] = $session['user_id'];
    $body['token']   = $session['token'];
    $body['type_id'] = self::TYPE_ID;

    $result = $this->post($body);

    // 498 = token expired mid-session → refresh and retry once
    if (498 === (int) ($result['status_code'] ?? 0)) {
      $this->clear_session();
      $refresh = $this->refresh_token();
      if (200 !== (int) ($refresh['status_code'] ?? 0)) {
        return $refresh;
      }
      $session         = $this->get_session();
      $body['user_id'] = $session['user_id'];
      $body['token']   = $session['token'];
      $result          = $this->post($body);
    }

    return $result;
  }

  // ------------------------------------------------------------------ 3. getCouponDetails
  public function get_coupon_details(string $coupon, float $invoice_amount): array
  {
    return $this->authenticated_post([
      'action'         => 'getCouponDetails',
      'coupon'         => $coupon,
      'invoice_amount' => (int) round($invoice_amount),
    ]);
  }

  // ------------------------------------------------------------------ 4. redeemCoupon
  public function redeem_coupon(string $coupon, float $invoice_amount, string $notes = ''): array
  {
    return $this->authenticated_post([
      'action'         => 'redeemCoupon',
      'coupon'         => $coupon,
      'invoice_amount' => (int) round($invoice_amount),
      'notes'          => $notes,
    ]);
  }

  // ------------------------------------------------------------------ 5. getSales
  public function get_sales(string $datetime_from, string $datetime_to, int $limit = 0): array
  {
    return $this->authenticated_post([
      'action'        => 'getSales',
      'datetime_from' => $datetime_from,
      'datetime_to'   => $datetime_to,
      'limit'         => $limit,
    ]);
  }

  // ------------------------------------------------------------------ 6. getDailySalesAmounts
  public function get_daily_sales_amounts(): array
  {
    return $this->authenticated_post([
      'action' => 'getDailySalesAmounts',
    ]);
  }
}
