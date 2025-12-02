<?php

/**
 * Rules.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'EFW_Multiple_Level_Rule' ) ) {

	/**
	 * EFW_Multiple_Level_Rule Class.
	 */
	class EFW_Multiple_Level_Rule extends EFW_Post {

		/**
		 * Post Type.
		 *
		 * @var string
		 */
		protected $post_type = EFW_Register_Post_Type::MULTIPLE_FEE_RULES_POSTTYPE;

		/**
		 * Post Status.
		 *
		 * @var string
		 */
		protected $post_status = 'publish';

		/**
		 * Gateway ID.
		 *
		 * @var string
		 */
		protected $efw_gateway_id;

		/**
		 * Fee Name.
		 *
		 * @var string
		 */
		protected $efw_name;

		/**
		 * User Filter Type.
		 *
		 * @var string
		 */
		protected $efw_user_filter_type;

		/**
		 * Include User.
		 *
		 * @var string
		 */
		protected $efw_include_user;

		/**
		 * Exclude User.
		 *
		 * @var string
		 */
		protected $efw_exclude_user;

		/**
		 * Include User Role.
		 *
		 * @var string
		 */
		protected $efw_include_user_role;

		/**
		 * Exclude User Role.
		 *
		 * @var string
		 */
		protected $efw_exclude_user_role;

		/**
		 * Product Filter Type.
		 *
		 * @var string
		 */
		protected $efw_product_filter_type;

		/**
		 * Include Product.
		 *
		 * @var string
		 */
		protected $efw_include_product;

		/**
		 * Exclude Product.
		 *
		 * @var int
		 */
		protected $efw_exclude_product;

		/**
		 * Include Category.
		 *
		 * @var int
		 */
		protected $efw_include_category;

		/**
		 * Include Category.
		 *
		 * @var int
		 */
		protected $efw_exclude_category;

		/**
		 * Include Additional Product.
		 *
		 * @var string
		 */
		protected $efw_additional_include_products;

		/**
		 * Exclude Additional Product.
		 *
		 * @var string
		 */
		protected $efw_additional_exclude_products;

		/**
		 * Fee Based on.
		 *
		 * @var string
		 */
		protected $efw_fee_based_on;

		/**
		 * Included Country.
		 *
		 * @var string
		 */
		protected $efw_included_country;

		/**
		 * Included States.
		 *
		 * @var string
		 */
		protected $efw_included_states;

		/**
		 * From Date.
		 *
		 * @var string
		 */
		protected $efw_from_date;

		/**
		 * TO Date.
		 *
		 * @var string
		 */
		protected $efw_to_date;

		/**
		 * Weekdays.
		 *
		 * @var string
		 */
		protected $efw_weekdays;

		/**
		 * Fee Text.
		 *
		 * @var string
		 */
		protected $efw_fee_text;

		/**
		 * Fee Description.
		 *
		 * @var string
		 */
		protected $efw_fee_description;

		/**
		 * Tax Class.
		 *
		 * @var string
		 */
		protected $efw_tax_class;

		/**
		 * Fee Type.
		 *
		 * @var string
		 */
		protected $efw_fee_type;

		/**
		 * Percentage Type.
		 *
		 * @var string
		 */
		protected $efw_percentage_type;

		/**
		 * Percentage Fee Type.
		 *
		 * @var string
		 */
		protected $efw_percentage_fee_type;

		/**
		 * Fixed Value.
		 *
		 * @var string
		 */
		protected $efw_fixed_value;

		/**
		 * Percent Value.
		 *
		 * @var string
		 */
		protected $efw_percent_value;

		/**
		 * Add Fixed Fee Value.
		 *
		 * @var string
		 */
		protected $efw_add_fixed_fee;

		/**
		 * Minimum Fee.
		 *
		 * @var string
		 */
		protected $efw_min_fee;

		/**
		 * Maximum Fee.
		 *
		 * @var string
		 */
		protected $efw_max_fee;

		/**
		 * Minimum Subtotal Fee.
		 *
		 * @var string
		 */
		protected $efw_min_sub_total;

		/**
		 * Maximum Subtotal Fee.
		 *
		 * @var string
		 */
		protected $efw_max_sub_total;

		/**
		 * Minimum Order Total Fee.
		 *
		 * @var string
		 */
		protected $efw_min_order_total;

		/**
		 * Maximum Order Total Fee.
		 *
		 * @var string
		 */
		protected $efw_max_order_total;

		/**
		 * Meta data keys
		 *
		 * @var array
		 */
		protected $meta_data_keys = array(
			'efw_name'            => '',
			'efw_user_filter_type' => '',
			'efw_include_user' => '',
			'efw_exclude_user' => '',
			'efw_include_user_role'        => '',
			'efw_exclude_user_role' => '',
			'efw_product_filter_type'        => '1',
			'efw_include_product'             => '',
			'efw_exclude_product'             => '',
			'efw_include_category'            => '',
			'efw_exclude_category'            => '',
			'efw_additional_include_products' => '',
			'efw_additional_exclude_products' => '',
			'efw_fee_based_on'  => '',
			'efw_included_country' => '',
			'efw_included_states' => '',
			'efw_from_date' => '',
			'efw_to_date' => '',
			'efw_weekdays' => '',
			'efw_fee_text' => '',
			'efw_fee_description' => '',
			'efw_tax_class' => '',
			'efw_fee_type' => '',
			'efw_percentage_type' => '',
			'efw_percentage_fee_type' => '',
			'efw_fixed_value' => '',
			'efw_percent_value' => '',
			'efw_add_fixed_fee' => '',
			'efw_min_fee' => '',
			'efw_max_fee' => '',
			'efw_min_sub_total' => '',
			'efw_max_sub_total' => '',
			'efw_min_order_total' => '',
			'efw_max_order_total' => '',
		);

		/**
		 * Prepare extra post data
		 */
		protected function load_extra_postdata() {
			$this->efw_gateway_id = $this->post->post_content ;
		}

		/**
		 * Set Id.
		 */
		public function set_id( $value ) {

			$this->id = $value;
		}

		/**
		 * Set Fee Name.
		 */
		public function set_name( $value ) {

			$this->efw_name = $value;
		}

		/**
		 * Set User Filter Type.
		 */
		public function set_user_filter_type( $value ) {

			$this->efw_user_filter_type = $value;
		}

				/**
				 * Set Include User.
				 */
		public function set_include_user( $value ) {

			$this->efw_include_user = $value;
		}

		/**
		 * Set Exclude User.
		 */
		public function set_exclude_user( $value ) {

			$this->efw_exclude_user = $value;
		}

		/**
		 * Set Include User Role.
		 */
		public function set_include_user_role( $value ) {

			$this->efw_include_user_role = $value;
		}

		/**
		 * Set Exclude User Role.
		 */
		public function set_exclude_user_role( $value ) {

			$this->efw_exclude_user_role = $value;
		}

		/**
		 * Set Product Filter Type.
		 */
		public function set_product_filter_type( $value ) {

			$this->efw_product_filter_type = $value;
		}

		/**
		 * Set Include Product.
		 */
		public function set_include_product( $value ) {

			$this->efw_include_product = $value;
		}

		/**
		 * Set Exclude Product.
		 */
		public function set_exclude_product( $value ) {

			$this->efw_exclude_product = $value;
		}

		/**
		 * Set Include Category.
		 */
		public function set_include_category( $value ) {

			$this->efw_include_category = $value;
		}

		/**
		 * Set Include Category.
		 */
		public function set_exclude_category( $value ) {

			$this->efw_exclude_category = $value;
		}

		/**
		 * Set Additional Products to Include.
		 */
		public function set_additional_include_products( $value ) {

			$this->efw_additional_include_products = $value;
		}

		/**
		 * Set Additional Products to Exclude.
		 */
		public function set_additional_exclude_products( $value ) {

			$this->efw_additional_exclude_products = $value;
		}

		/**
		 * Set Fee based on.
		 */
		public function set_fee_based_on( $value ) {

			$this->efw_fee_based_on = $value;
		}

		/**
		 * Set Included Country.
		 */
		public function set_included_country( $value ) {

			$this->efw_included_country = $value;
		}

		/**
		 * Set Included States.
		 */
		public function set_included_states( $value ) {

			$this->efw_included_states = $value;
		}

		/**
		 * Set From Date.
		 */
		public function set_from_date( $value ) {

			$this->efw_from_date = $value;
		}

		/**
		 * Set To Date.
		 */
		public function set_to_date( $value ) {

			$this->efw_to_date = $value;
		}

		/**
		 * Set Weekdays.
		 */
		public function set_weekdays( $value ) {

			$this->efw_weekdays = $value;
		}

		/**
		 * Set Fee Text.
		 */
		public function set_fee_text( $value ) {

			$this->efw_fee_text = $value;
		}

		/**
		 * Set Fee Description.
		 */
		public function set_fee_description( $value ) {

			$this->efw_fee_description = $value;
		}

		/**
		 * Set TAc Class.
		 */
		public function set_tax_class( $value ) {

			$this->efw_tax_class = $value;
		}

		/**
		 * Set Fee Type.
		 */
		public function set_fee_type( $value ) {

			$this->efw_fee_type = $value;
		}

		/**
		 * Set Percentage Type.
		 */
		public function set_percentage_type( $value ) {

			$this->efw_percentage_type = $value;
		}

		/**
		 * Set Percentage Fee Type.
		 */
		public function set_percentage_fee_type( $value ) {

			$this->efw_percentage_fee_type = $value;
		}

		/**
		 * Set Fixed Value.
		 */
		public function set_fixed_value( $value ) {

			$this->efw_fixed_value = $value;
		}

		/**
		 * Set Percent Value.
		 */
		public function set_percent_value( $value ) {

			$this->efw_percent_value = $value;
		}

		/**
		 * Set Add Fixed Fee Value.
		 */
		public function set_add_fixed_fee( $value ) {

			$this->efw_add_fixed_fee = $value;
		}

		/**
		 * Set Minimum Fee Value.
		 */
		public function set_min_fee( $value ) {

			$this->efw_min_fee = $value;
		}

		/**
		 * Set Maximum Fee Value.
		 */
		public function set_max_fee( $value ) {

			$this->efw_max_fee = $value;
		}

		/**
		 * Set Minimum Subtotal Value.
		 */
		public function set_min_sub_total( $value ) {

			$this->efw_min_sub_total = $value;
		}

		/**
		 * Set Maximum Sub Total Value.
		 */
		public function set_max_sub_total( $value ) {

			$this->efw_max_sub_total = $value;
		}

		/**
		 * Set Minimum Order Total Value.
		 */
		public function set_min_order_total( $value ) {

			$this->efw_min_order_total = $value;
		}

		/**
		 * Set Maximum Order Total Value.
		 */
		public function set_max_order_total( $value ) {

			$this->efw_max_order_total = $value;
		}

		/**
		 * Get Id.
		 */
		public function get_id() {

			return $this->id;
		}

		/**
		 * Get Name.
		 */
		public function get_name() {

			return $this->efw_name;
		}

		/**
		 * Get Name.
		 */
		public function get_gateway_id() {

			return $this->efw_gateway_id;
		}

		/**
		 * Get User Filter Type.
		 */
		public function get_user_filter_type() {

			return $this->efw_user_filter_type;
		}

				/**
				 * Get Include User.
				 */
		public function get_include_user() {

			return $this->efw_include_user;
		}

		/**
		 * Get Exclude User.
		 */
		public function get_exclude_user() {

			return $this->efw_exclude_user;
		}

		/**
		 * Get Include User Role.
		 */
		public function get_include_user_role() {

			return $this->efw_include_user_role;
		}

		/**
		 * Get Exclude User Role.
		 */
		public function get_exclude_user_role() {

			return $this->efw_exclude_user_role;
		}

		/**
		 * Get Product Filter Type.
		 */
		public function get_product_filter_type() {

			return $this->efw_product_filter_type;
		}

		/**
		 * Get Include Product.
		 */
		public function get_include_product() {

			return $this->efw_include_product;
		}

		/**
		 * Get Exclude Product.
		 */
		public function get_exclude_product() {

			return $this->efw_exclude_product;
		}

		/**
		 * Get Include Category.
		 */
		public function get_include_category() {

			return $this->efw_include_category;
		}

		/**
		 * Get Include Category.
		 */
		public function get_exclude_category() {

			return $this->efw_exclude_category;
		}

		/**
		 * Get Additional Products to Include.
		 */
		public function get_additional_include_products() {

			return $this->efw_additional_include_products;
		}

		/**
		 * Get Additional Products to Exclude.
		 */
		public function get_additional_exclude_products() {

			return $this->efw_additional_exclude_products;
		}

		/**
		 * Get Fee based on.
		 */
		public function get_fee_based_on() {

			return $this->efw_fee_based_on;
		}

		/**
		 * Get Included Country.
		 */
		public function get_included_country() {

			return $this->efw_included_country;
		}

		/**
		 * Get Included States.
		 */
		public function get_included_states() {

			return $this->efw_included_states;
		}

		/**
		 * Get From Date.
		 */
		public function get_from_date() {

			return $this->efw_from_date;
		}

		/**
		 * Get To Date.
		 */
		public function get_to_date() {

			return $this->efw_to_date;
		}

		/**
		 * Get Weekdays.
		 */
		public function get_weekdays() {

			return $this->efw_weekdays;
		}

		/**
		 * Get Fee Text.
		 */
		public function get_fee_text() {

			return $this->efw_fee_text;
		}

		/**
		 * Get Fee Description.
		 */
		public function get_fee_description() {

			return $this->efw_fee_description;
		}

		/**
		 * Get Tax Class.
		 */
		public function get_tax_class() {

			return $this->efw_tax_class;
		}

		/**
		 * Get Fee Type.
		 */
		public function get_fee_type() {

			return $this->efw_fee_type;
		}

		/**
		 * Get Percentage Type.
		 */
		public function get_percentage_type() {

			return $this->efw_percentage_type;
		}

		/**
		 * Get Percentage Fee Type.
		 */
		public function get_percentage_fee_type() {

			return $this->efw_percentage_fee_type;
		}

		/**
		 * Get Fixed Value.
		 */
		public function get_fixed_value() {

			return $this->efw_fixed_value;
		}

		/**
		 * Get Percent Value.
		 */
		public function get_percent_value() {

			return $this->efw_percent_value;
		}

		/**
		 * Get Add Fixed Fee Value.
		 */
		public function get_add_fixed_fee() {

			return $this->efw_add_fixed_fee;
		}

		/**
		 * Get Minimum Fee Value.
		 */
		public function get_min_fee() {

			return $this->efw_min_fee;
		}

		/**
		 * Get Maximum Fee Value.
		 */
		public function get_max_fee() {

			return $this->efw_max_fee;
		}

		/**
		 * Get Minimum Subtotal Value.
		 */
		public function get_min_sub_total() {

			return $this->efw_min_sub_total;
		}

		/**
		 * Get Maximum Sub Total Value.
		 */
		public function get_max_sub_total() {

			return $this->efw_max_sub_total;
		}

		/**
		 * Get Minimum Order Total Value.
		 */
		public function get_min_order_total() {

			return $this->efw_min_order_total;
		}

		/**
		 * Get Maximum Order Total Value.
		 */
		public function get_max_order_total() {

			return $this->efw_max_order_total;
		}
	}

}
