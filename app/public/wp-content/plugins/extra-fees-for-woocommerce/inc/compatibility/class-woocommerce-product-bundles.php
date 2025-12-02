<?php
/**
 * Compatibility - WooCommerce Product Bundles
 *
 * Tested upto: 2.6.1
 *
 * @since 7.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EFW_WC_Product_Bundles_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 6.3.0
	 */
	class EFW_WC_Product_Bundles_Compatibility extends EFW_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 6.3.0
		 */
		public function __construct() {
			$this->id = 'woocommerce_product_bundles';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 6.3.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return class_exists( 'WC_Bundles' );
		}

		/**
		 * Actions.
		 *
		 * @since 6.3.0
		 */
		public function frontend_action() {
			add_action( 'efw_validate_products', array( __CLASS__, 'check_if_bundles_product' ), 10, 3 );
			add_filter( 'efw_product_fee_for_bundled_product', array( __CLASS__, 'get_fee_for_bundles_product' ), 10, 2 );
		}

		/**
		 * Check if it is bundles product.
		 *
		 * @return bool
		 * */
		public static function check_if_bundles_product( $bool, $value, $product ) {
			$apply_fee_on = get_option('efw_productfee_apply_fee_for_bundles_on', '1');
			if ( '1' ==  $apply_fee_on) {
				if ( isset( $value['bundled_by'] ) && '' !== $value['bundled_by'] ) {
					return false;
				}
			} elseif ( 'bundle' == $product->get_type() && ! isset( $value['bundled_by'] ) ) {
					return false;
			}

			return $bool ;
		}

		/**
		 * Get Fee for Bundles Product.
		 *
		 * @return void
		 * */
		public static function get_fee_for_bundles_product( $product_fee, $product ) {
			if ('1' == get_option('efw_productfee_apply_fee_for_bundles_on', '1')) {
				return $product_fee;
			}

			if ('bundle' != $product->get_type()) {
				return $product_fee;
			}

			$bundled_items = $product->get_bundled_items();
			if ( !efw_check_is_array($bundled_items)) {
				return $product_fee;
			}

			$bundled_product_fee = 0;
			foreach ($bundled_items as $bundled_item) {
				$product_id = $bundled_item->get_product_id();
				$bundled_product = wc_get_product( $product_id );
				$bundled_product_fee += EFW_Fees_Handler::product_fee( $product_id, $bundled_product->get_price(), get_current_user_id(), 'fee' );
			}
			
			return $bundled_product_fee;
		}
	}

}
