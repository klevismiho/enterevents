<?php

namespace _PhpScoper7148efed0ae7;

/**
 * WC_Gateway_Pok class
 *
 * @author   RPay
 * @package  WooCommerce Pok Payments Gateway
 * @since    1.0.0
 */
// Exit if accessed directly.
if (!\defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/../vendor/autoload.php';
use _PhpScoper7148efed0ae7\RPay\POK\PaymentsSdk\Api\MerchantsApi;
use _PhpScoper7148efed0ae7\RPay\POK\PaymentsSdk\Configuration;
use _PhpScoper7148efed0ae7\RPay\POK\PaymentsSdk\Api\AuthApi;
use _PhpScoper7148efed0ae7\RPay\POK\PaymentsSdk\Model\LoginSdkPayload;
/**
 * Pok Gateway.
 *
 * @class    WC_Gateway_Pok
 * @version  1.1.0
 */
class WC_Gateway_Pok extends \WC_Payment_Gateway
{
    /**
     * Payment gateway instructions.
     * @var string
     *
     */
    protected $instructions;
    /**
     * Whether the gateway is visible for non-admin users.
     * @var boolean
     *
     */
    protected $hide_for_non_admin_users;
    /**
     * Unique id for the gateway.
     * @var string
     *
     */
    public $id = 'pok';
    private $keyId;
    private $keySecret;
    private $merchantId;
    private $useSandbox = \TRUE;
    private $configuration;
    private $httpClient = NULL;
    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
        $this->icon = trailingslashit(\WP_PLUGIN_URL) . plugin_basename(\dirname(__FILE__)) . '/img/pok.png';
        $this->has_fields = \false;
        $this->supports = array('pre-orders', 'products');
        $this->method_title = _x('Pok Payment', 'Pok payment method', 'woocommerce-gateway-pok');
        $this->method_description = __('Reliable Card and POK payments', 'woocommerce-gateway-pok');
        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();
        // Define user set variables.
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->instructions = $this->get_option('instructions', $this->description);
        $this->hide_for_non_admin_users = $this->get_option('hide_for_non_admin_users');
        $this->useSandbox = \strcmp($this->get_option('testApi'), 'yes') == 0;
        $this->configuration = Configuration::getDefaultConfiguration(!$this->useSandbox);
        $this->httpClient = new GuzzleHttp\Client();
        // Actions.
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_pok', array($this, 'update_order_status'));
    }
    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
        $this->form_fields = array('enabled' => array('title' => __('Enable/Disable', 'woocommerce'), 'type' => 'checkbox', 'label' => __('Enable POK Payment', 'woocommerce'), 'default' => 'no'), 'title' => array('title' => __('Title', 'woocommerce'), 'type' => 'text', 'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'), 'default' => __('POKPAY', 'woocommerce'), 'desc_tip' => \true), 'description' => array('title' => __('Customer Message', 'woocommerce'), 'type' => 'textarea', 'default' => ''), 'apiKeyId' => array('title' => __('POK API keyId', 'woocommerce'), 'type' => 'text', 'description' => __('This supplies the api key id for POK. It can be retrieved from POK merchant panel.', 'woocommerce'), 'default' => ''), 'apiKeySecret' => array('title' => __('POK API keySecret', 'woocommerce'), 'type' => 'password', 'description' => __('This supplies the api key secret for POK. It can be retrieved from POK merchant panel.', 'woocommerce'), 'default' => ''), 'merchantId' => array('title' => __('POK API merchantId', 'woocommerce'), 'type' => 'text', 'description' => __('This supplies the merchant id for POK. It can be retrieved from POK merchant panel.', 'woocommerce'), 'default' => ''), 'testApi' => array('title' => __('Use POK test API', 'woocommerce'), 'type' => 'checkbox', 'description' => __('This specifies to use POK test API instead of live api.', 'woocommerce'), 'default' => 'yes'));
    }
    function setApiSettings()
    {
        $this->keyId = $this->get_option('apiKeyId');
        $this->keySecret = $this->get_option('apiKeySecret');
        $this->merchantId = $this->get_option('merchantId');
    }
    function loginSdk($keyId, $keySecret)
    {
        $authapi = new AuthApi($this->configuration, $this->httpClient);
        $payload = new LoginSdkPayload($keyId, $keySecret);
        try {
            $result = $authapi->login($payload);
            $this->configuration->setApiKey('Authorization', $result->getData()->getAccessToken());
            return \true;
        } catch (\Exception $e) {
            return \false;
        }
    }
    /**
     * Process the payment and return the result.
     *
     * @param  int  $order_id
     * @return array
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        $this->setApiSettings();
        try {
            $createdSdkOrder = $this->createSdkOrder($order);
        } catch (\Exception $e) {
            $this->update_option('enabled', 'no');
            $message = __('Order payment failed. To make a successful payment using Pok Payments, please review the gateway settings.', 'woocommerce-gateway-pok');
            throw new \Exception($message);
        }
        $order->update_status('on-hold', __('Awaiting POK payment', 'woocommerce'));
        // Remove cart
        WC()->cart->empty_cart();
        $redirectUrl = add_query_arg(array('firstName' => $order->get_billing_first_name(), 'lastName' => $order->get_billing_last_name(), 'email' => $order->get_billing_email(), 'country' => $order->get_billing_country(), 'state' => $order->get_billing_state(), 'city' => $order->get_billing_city(), 'phone' => $order->get_billing_phone(), 'address' => $order->get_billing_address_1(), 'zip' => $order->get_billing_postcode()), $createdSdkOrder->getSelf()->getConfirmUrl());
        return array('result' => 'success', 'redirect' => $redirectUrl);
    }
    public function update_order_status()
    {
        $orderId = $_GET['wcOrderId'];
        if (!isset($orderId) || !\is_numeric($orderId)) {
            return;
        }
        $order = wc_get_order($orderId);
        $order->payment_complete();
    }
    public function createSdkOrder($wcOrder)
    {
        $this->loginSdk($this->keyId, $this->keySecret);
        $merchantsApi = new MerchantsApi($this->merchantId, $this->configuration, $this->httpClient);
        $orderId = $wcOrder->get_id();
        $orderNumber = $wcOrder->get_order_number();
        $amount = $wcOrder->get_total() - $wcOrder->get_shipping_total();
        $webhookUrl = add_query_arg('wcOrderId', $orderId, home_url('/wc-api/pok'));
        $products = [];
        $productsAmount = 0;
        foreach ($wcOrder->get_items() as $item) {
            $itemName = $item->get_name();
            $itemQuantity = $item->get_quantity();
            $itemTotal = $item->get_total();
            $itemPrice = $itemTotal / $itemQuantity;
            $products[] = ['name' => $itemName, 'quantity' => $itemQuantity, 'price' => $itemPrice];
            $productsAmount += $itemTotal;
        }
        $sdkOrderInfo = ['description' => "WooCommerce order {$orderNumber}", 'currencyCode' => $wcOrder->get_currency(), 'autoCapture' => \true, 'webhookUrl' => $webhookUrl, 'redirectUrl' => $this->get_return_url($wcOrder), 'shippingCost' => $wcOrder->get_shipping_total(), 'merchantCustomReference' => "{$orderId}"];
        if ($productsAmount === $amount) {
            $sdkOrderInfo['products'] = $products;
        } else {
            $sdkOrderInfo['amount'] = $amount;
        }
        $body = new \_PhpScoper7148efed0ae7\RPay\POK\PaymentsSdk\Model\CreateSdkOrderPayload($sdkOrderInfo);
        $result = $merchantsApi->createOrder($body);
        $createdSdkOrder = $result->getData()->getSdkOrder();
        return $createdSdkOrder;
    }
}
/**
 * Pok Gateway.
 *
 * @class    WC_Gateway_Pok
 * @version  1.1.0
 */
\class_alias('_PhpScoper7148efed0ae7\\WC_Gateway_Pok', 'WC_Gateway_Pok', \false);
