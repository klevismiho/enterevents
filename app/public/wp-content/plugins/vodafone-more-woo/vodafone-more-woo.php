<?php
/**
 * Plugin Name: Vodafone More – WooCommerce Integration
 * Plugin URI:  https://klevismiho.com
 * Description: Integrates the Vodafone More loyalty coupon API with WooCommerce checkout.
 * Version:     1.0
 * Author:      Klevis Miho
 * Text Domain: vodafone-more-woo
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants
define( 'VMW_VERSION',    '1.0' );
define( 'VMW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VMW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


add_action( 'plugins_loaded', function () {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p>
                <strong>Vodafone More WooCommerce</strong> requires WooCommerce to be installed and active.
            </p></div>';
        } );
        return;
    }

    require_once VMW_PLUGIN_DIR . 'includes/class-vmw-api.php';
    require_once VMW_PLUGIN_DIR . 'includes/class-vmw-settings.php';
    require_once VMW_PLUGIN_DIR . 'includes/class-vmw-checkout.php';

    VMW_Settings::instance();
    VMW_Checkout::instance();
} );