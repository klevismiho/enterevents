<?php

/**
 * Handles the order reports exports.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_CSV_Exporter' , false ) ) {
	require_once WC_ABSPATH . 'includes/export/abstract-wc-csv-exporter.php' ;
}

if ( ! class_exists( 'EFW_Export_Order_Report_CSV' ) ) {

	/**
	 * EFW_Export_Order_Report_CSV.
	 */
	class EFW_Export_Order_Report_CSV extends WC_CSV_Exporter {

		/**
		 * Type of export used in filter names.
		 *
		 * @var string
		 */
		protected $export_type = 'order_reports' ;

		/**
		 * Filename to export to.
		 *
		 * @var string
		 */
		protected $filename = 'order-reports.csv' ;

		/**
		 * Database
		 *
		 * @var string
		 * */
		private $database;

		/**
		 * Post type
		 *
		 * @var string
		 * */
		private $post_type = EFW_Register_Post_Type::FEES_POSTTYPE;

		/**
		 * Return default columns.
		 * 
		 * @return array
		 */
		public function get_default_column_names() {

			$headings = array(
				'order_id'  => esc_html__( 'Order ID' , 'extra-fees-for-woocommerce' ),
				'product_fee' => esc_html__( 'Product Fee' , 'extra-fees-for-woocommerce' ),
				'gateway_fee' => esc_html__( 'Payment Gateway Fee' , 'extra-fees-for-woocommerce' ),
				'order_fee' => esc_html__( 'Order Fee' , 'extra-fees-for-woocommerce' ),
				'shipping_fee' => esc_html__( 'Shipping Fee' , 'extra-fees-for-woocommerce' ),
				'additional_fee' => esc_html__( 'Additional Fee' , 'extra-fees-for-woocommerce' ),
				'total_fee'  => esc_html__( 'Total Fee' , 'extra-fees-for-woocommerce' ),
					) ;

			/**
			 * Hook:efw_export_order_reports_heading. 
			 *
			 * @since 7.3.0
			 */
			return apply_filters( 'efw_export_order_reports_heading' , $headings ) ;
		}

		/**
		 * Prepare data that will be exported.
		 * 
		 * @return void.
		 */
		public function prepare_data_to_export() {

			global $wpdb;
			$this->database = &$wpdb; 
			// Prepare column names.
			$this->column_names = $this->get_default_column_names() ;

			$fee_ids = $this->database->get_col( 
					'SELECT DISTINCT ID FROM ' . $this->database->posts . ' as p
					INNER JOIN ' . $this->database->postmeta . ' as meta ON p.ID=meta.post_id
					INNER JOIN ' . $this->database->postmeta . " as meta1 ON p.ID=meta1.post_id
					AND meta.meta_key = 'efw_order_id' AND meta.meta_value != '0'
					AND meta1.meta_key IN ('efw_product_fee','efw_gateway_fee','efw_shipping_fee', 'efw_order_fee', 'efw_additional_fee')
					AND meta1.meta_value NOT IN('0','0','0','0','0')
					WHERE post_type='" . $this->post_type . "' AND p.post_status='publish' GROUP BY meta.meta_value ORDER BY ID DESC"  ) ;

			foreach ( $fee_ids as $fee_id ) {

				$fee_data = efw_get_fees( $fee_id ) ;
				if ( ! is_object( $fee_data ) ) {
					continue ;
				}

				$this->row_data[] = self::generate_row_data( $fee_data ) ;
			}
		}

		/**
		 * Get the cashback data.
		 * 
		 * @return array
		 */
		protected function generate_row_data( $fee_data ) {

			$order_id = $fee_data->get_order_id();
			$row = array(
				'order_id'  => $fee_data->get_order_id(),
				'product_fee' => $this->get_product_fee( $order_id ),
				'gateway_fee' => $this->get_gateway_fee( $order_id ),
				'order_fee' => $this->get_order_fee( $order_id ),
				'shipping_fee' => $this->get_shipping_fee( $order_id ),
				'additional_fee' => $this->get_additional_fee( $order_id ),
				'total_fee'  => $this->get_product_fee($order_id) + $this->get_gateway_fee($order_id) + $this->get_order_fee($order_id) + $this->get_shipping_fee($order_id) + $this->get_additional_fee( $order_id ),
					) ;

			/**
			 * Hook:cbk_cashback_log_export_row_data. 
			 *
			 * @since 1.0
			 */
			return apply_filters( 'cbk_cashback_log_export_row_data' , $row ) ;
		}

		/**
		 * Get Product Fee
		 * */
		private function get_product_fee( $order_id ) {
			$prepare_query = $this->database->prepare( 
				'SELECT SUM(meta1.meta_value) FROM ' . $this->database->posts . ' as p 
				INNER JOIN ' . $this->database->postmeta . ' as meta ON p.ID=meta.post_id
				INNER JOIN ' . $this->database->postmeta . " as meta1 ON p.ID=meta1.post_id 
				where p.post_type='" . $this->post_type . "' and p.post_status IN('publish')
				AND meta.meta_key = 'efw_order_id' AND meta.meta_value = %d
				AND meta1.meta_key = 'efw_product_fee'
				AND meta1.meta_value != '0'
				GROUP BY meta.meta_value", $order_id
			);

			$product_fee = $this->database->get_col( $prepare_query );
			
			return efw_check_is_array($product_fee) ? array_sum($product_fee) : 0;
		}

		/**
		 * Get Order Fee
		 * */
		private function get_order_fee( $order_id ) {
			$prepare_query = $this->database->prepare( 
				'SELECT SUM(meta1.meta_value) FROM ' . $this->database->posts . ' as p 
				INNER JOIN ' . $this->database->postmeta . ' as meta ON p.ID=meta.post_id
				INNER JOIN ' . $this->database->postmeta . " as meta1 ON p.ID=meta1.post_id 
				where p.post_type='" . $this->post_type . "' and p.post_status IN('publish')
				AND meta.meta_key = 'efw_order_id' AND meta.meta_value = %d
				AND meta1.meta_key = 'efw_order_fee'
				AND meta1.meta_value != '0'
				GROUP BY meta.meta_value", $order_id
			);

			$order_fee = $this->database->get_col( $prepare_query );
			
			return efw_check_is_array($order_fee) ? array_sum($order_fee) : 0;
		}

		/**
		 * Get Gateway Fee
		 * */
		private function get_gateway_fee( $order_id ) {
			$prepare_query = $this->database->prepare( 
				'SELECT SUM(meta1.meta_value) FROM ' . $this->database->posts . ' as p 
				INNER JOIN ' . $this->database->postmeta . ' as meta ON p.ID=meta.post_id
				INNER JOIN ' . $this->database->postmeta . " as meta1 ON p.ID=meta1.post_id 
				where p.post_type='" . $this->post_type . "' and p.post_status IN('publish')
				AND meta.meta_key = 'efw_order_id' AND meta.meta_value = %d
				AND meta1.meta_key = 'efw_gateway_fee'
				AND meta1.meta_value != '0'
				GROUP BY meta.meta_value", $order_id
			);

			$gateway_fee = $this->database->get_col( $prepare_query );
			
			return efw_check_is_array($gateway_fee) ? array_sum($gateway_fee) : 0;
		}

		/**
		 * Get Shipping Fee
		 * */
		private function get_shipping_fee( $order_id ) {
			$prepare_query = $this->database->prepare( 
				'SELECT SUM(meta1.meta_value) FROM ' . $this->database->posts . ' as p 
				INNER JOIN ' . $this->database->postmeta . ' as meta ON p.ID=meta.post_id
				INNER JOIN ' . $this->database->postmeta . " as meta1 ON p.ID=meta1.post_id 
				where p.post_type='" . $this->post_type . "' and p.post_status IN('publish')
				AND meta.meta_key = 'efw_order_id' AND meta.meta_value = %d
				AND meta1.meta_key = 'efw_shipping_fee'
				AND meta1.meta_value != '0'
				GROUP BY meta.meta_value", $order_id
			);

			$shipping_fee = $this->database->get_col( $prepare_query );
			
			return efw_check_is_array($shipping_fee) ? array_sum($shipping_fee) : 0;
		}

		/**
		 * Get Additional Fee
		 * */
		private function get_additional_fee( $order_id ) {
			$prepare_query = $this->database->prepare( 
				'SELECT SUM(meta1.meta_value) FROM ' . $this->database->posts . ' as p 
				INNER JOIN ' . $this->database->postmeta . ' as meta ON p.ID=meta.post_id
				INNER JOIN ' . $this->database->postmeta . " as meta1 ON p.ID=meta1.post_id 
				where p.post_type='" . $this->post_type . "' and p.post_status IN('publish')
				AND meta.meta_key = 'efw_order_id' AND meta.meta_value = %d
				AND meta1.meta_key = 'efw_additional_fee'
				AND meta1.meta_value != '0'
				GROUP BY meta.meta_value", $order_id
			);

			$additional_fee = $this->database->get_col( $prepare_query );
			
			return efw_check_is_array($additional_fee) ? array_sum($additional_fee) : 0;
		}
	}

}
