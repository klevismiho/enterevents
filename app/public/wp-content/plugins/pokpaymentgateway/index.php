<?php

namespace _PhpScoper7148efed0ae7;

/*
* Plugin Name: POK Payment Gateway
* Author: RPay 
* Author URI: http://pokpay.io/
* Version: 1.1.0
* Description: POK Payment Gateway for WooCommerce
* WC requires at least: 6.8.0
* WC tested up to: 8.8.3
* Text Domain: pok-payment-gateway
* 
* @package PokPay
*/
// Exit if accessed directly.
if (!\defined('ABSPATH')) {
    exit;
}
/**
 * WC Pok Payment gateway plugin class.
 *
 * @class WC_Pok_Payments
 */
class WC_Pok_Payments
{
    /**
     * Plugin bootstrapping.
     */
    public static function init()
    {
        add_action('plugins_loaded', array(__CLASS__, 'includes'), 0);
        add_filter('woocommerce_payment_gateways', array(__CLASS__, 'add_gateway'));
        add_action('woocommerce_blocks_loaded', array(__CLASS__, 'woocommerce_gateway_pok_woocommerce_block_support'));
        add_action('before_woocommerce_init', function () {
            if (\class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, \true);
            }
        });
    }
    /**
     * Add the Pok Payment gateway to the list of available gateways.
     *
     * @param array
     */
    public static function add_gateway($gateways)
    {
        $gateways[] = 'WC_Gateway_Pok';
        return $gateways;
    }
    /**
     * Plugin includes.
     */
    public static function includes()
    {
        // Make the WC_Gateway_Pok class available.
        if (\class_exists('\WC_Payment_Gateway')) {
            require_once 'includes/class-wc-gateway-pok.php';
        }
    }
    /**
     * Plugin url.
     *
     * @return string
     */
    public static function plugin_url()
    {
        return untrailingslashit(plugins_url('/', __FILE__));
    }
    /**
     * Plugin url.
     *
     * @return string
     */
    public static function plugin_abspath()
    {
        return trailingslashit(plugin_dir_path(__FILE__));
    }
    /**
     * Registers WooCommerce Blocks integration.
     *
     */
    public static function woocommerce_gateway_pok_woocommerce_block_support()
    {
        if (\class_exists('\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            require_once 'includes/blocks/class-wc-pok-payments-blocks.php';
            add_action('woocommerce_blocks_payment_method_type_registration', function (\Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
                $payment_method_registry->register(new WC_Gateway_Pok_Blocks_Support());
            });
        }
    }
}
/**
 * WC Pok Payment gateway plugin class.
 *
 * @class WC_Pok_Payments
 */
\class_alias('_PhpScoper7148efed0ae7\\WC_Pok_Payments', 'WC_Pok_Payments', \false);
WC_Pok_Payments::init();
