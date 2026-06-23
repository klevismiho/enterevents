<?php
defined('ABSPATH') || exit;

class VMW_Checkout
{
    private static ?VMW_Checkout $instance = null;

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // Render the Vodafone More coupon field at checkout
        add_action('woocommerce_before_order_notes', [$this, 'render_coupon_field']);

        // AJAX handlers
        add_action('wp_ajax_vmw_apply_coupon',        [$this, 'ajax_apply_coupon']);
        add_action('wp_ajax_nopriv_vmw_apply_coupon', [$this, 'ajax_apply_coupon']);
        add_action('wp_ajax_vmw_remove_coupon',        [$this, 'ajax_remove_coupon']);
        add_action('wp_ajax_nopriv_vmw_remove_coupon', [$this, 'ajax_remove_coupon']);

        // Apply fee when cart totals are calculated
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_fee']);

        // Enqueue JS & CSS
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    // ------------------------------------------------------------------ enqueue
    public function enqueue_scripts(): void
    {
        if (! is_checkout()) return;

        wp_enqueue_script(
            'vmw-checkout',
            VMW_PLUGIN_URL . 'assets/checkout.js',
            ['jquery'],
            VMW_VERSION,
            true
        );

        wp_localize_script('vmw-checkout', 'vmwData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('vmw_coupon_nonce'),
            'i18n'     => [
                'applying' => __('Applying…', 'vodafone-more-woo'),
                'removing' => __('Removing…', 'vodafone-more-woo'),
            ],
        ]);

        wp_enqueue_style(
            'vmw-checkout',
            VMW_PLUGIN_URL . 'assets/checkout.css',
            [],
            VMW_VERSION
        );
    }

    // ------------------------------------------------------------------ render field
    public function render_coupon_field(): void
    {
        $active = WC()->session->get('vmw_active_coupon');
        ?>
        <div id="vmw-coupon-wrap">
            <h3><?php esc_html_e('Vodafone More Discount', 'vodafone-more-woo'); ?></h3>

            <div id="vmw-coupon-inner">
                <?php if (! empty($active)) : ?>
                    <!-- Coupon already applied -->
                    <div class="vmw-applied">
                        <p class="vmw-success">
                            ✅ <?php printf(
                                esc_html__('Coupon %s applied! You save %s ALL.', 'vodafone-more-woo'),
                                '<strong>' . esc_html($active['code']) . '</strong>',
                                '<strong>' . number_format($active['discount'], 2) . '</strong>'
                            ); ?>
                        </p>
                        <button type="button" id="vmw-remove-btn" class="button">
                            <?php esc_html_e('Remove', 'vodafone-more-woo'); ?>
                        </button>
                    </div>
                <?php else : ?>
                    <!-- Coupon input -->
                    <p><?php esc_html_e('Have a Vodafone More loyalty coupon? Enter it below.', 'vodafone-more-woo'); ?></p>
                    <div class="vmw-input-row">
                        <input type="text"
                               id="vmw_coupon_code"
                               placeholder="<?php esc_attr_e('8-digit coupon code', 'vodafone-more-woo'); ?>"
                               maxlength="8"
                               class="input-text">
                        <button type="button" id="vmw-apply-btn" class="button alt">
                            <?php esc_html_e('Apply', 'vodafone-more-woo'); ?>
                        </button>
                    </div>
                    <div id="vmw-coupon-message"></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    // ------------------------------------------------------------------ AJAX: apply
    public function ajax_apply_coupon(): void
    {
        check_ajax_referer('vmw_coupon_nonce', 'nonce');

        $code = sanitize_text_field($_POST['coupon'] ?? '');

        if (empty($code)) {
            wp_send_json_error(['message' => __('Please enter a coupon code.', 'vodafone-more-woo')]);
        }

        if (! preg_match('/^\d{8}$/', $code)) {
            wp_send_json_error(['message' => __('Vodafone More coupon codes are 8 digits.', 'vodafone-more-woo')]);
        }

        $cart_total = floatval(WC()->cart->get_subtotal());
        $api        = VMW_API::instance();
        $result     = $api->get_coupon_details($code, $cart_total);

        if (200 !== (int) ($result['status_code'] ?? 0)) {
            wp_send_json_error([
                'message' => $result['status_message'] ?? __('Coupon is not valid.', 'vodafone-more-woo'),
            ]);
        }

        $discount = floatval($result['data']['invoice']['discount'] ?? 0);
        $promo    = $result['data']['product']['product'] ?? '';

        if ($discount <= 0) {
            wp_send_json_error([
                'message' => __('This coupon offers no discount for your current cart.', 'vodafone-more-woo'),
            ]);
        }

        // Store in session
        WC()->session->set('vmw_active_coupon', [
            'code'     => $code,
            'discount' => $discount,
            'promo'    => $promo,
        ]);

        wp_send_json_success([
            'message' => sprintf(
                __('Coupon %1$s applied! You save %2$s %3$s.', 'vodafone-more-woo'),
                $code,
                number_format($discount, 2),
                get_woocommerce_currency_symbol()
            ),
            'discount' => $discount,
            'promo'    => $promo,
            'code'     => $code,
        ]);
    }

    // ------------------------------------------------------------------ AJAX: remove
    public function ajax_remove_coupon(): void
    {
        check_ajax_referer('vmw_coupon_nonce', 'nonce');
        WC()->session->set('vmw_active_coupon', null);
        wp_send_json_success();
    }

    // ------------------------------------------------------------------ apply fee on cart
    public function apply_fee(\WC_Cart $cart): void
    {
        if (is_admin() && ! defined('DOING_AJAX')) return;

        $data = WC()->session->get('vmw_active_coupon');
        if (empty($data)) return;

        $cart->add_fee(
            sprintf('Vodafone More: %s', $data['promo']),
            -floatval($data['discount']),
            false
        );
    }
}