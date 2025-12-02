<?php
/* Admin HTML Fees Settings for Simple Product */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="efw-multiple-level-content-wrapper">
	<h3 class="efw-multiple-level-rule-name">
		<?php
			echo ! empty( $rule->get_name() ) ? esc_html( $rule->get_name() ) : esc_html__( 'Untitled', 'extra-fees-for-woocommerce' );
		?>
		<span class="dashicons dashicons-arrow-down"></span>
		<span class="dashicons dashicons-trash efw-delete-multiple-level-rule" data-ruleid="<?php echo esc_attr( $multilevel_rule_id ); ?>"></span>
	</h3>
	<div class="efw-multiple-level-rule-fields efw-multiple-level-fee-wrapper">
		<p class="form-field">
			<label><?php esc_html_e( 'Rule Name', 'extra-fees-for-woocommerce' ); ?><span class='required'>*</span></label>
			<input type="text" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_name]" value="<?php echo esc_attr( $rule->get_name() ); ?>"/>
		</p>
		<p class="form-field">
			<label><?php esc_html_e( 'Fee should apply for', 'extra-fees-for-woocommerce' ); ?><span class='required'>*</span></label>
			<select class="efw-user-filter-type-for-multilevel" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_user_filter_type]">
				<option value="1" <?php echo selected( $rule->get_user_filter_type(), '1', true ); ?>><?php esc_html_e( 'All User(s)', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="2" <?php echo selected( $rule->get_user_filter_type(), '2', true ); ?>><?php esc_html_e( 'Include User(s)', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="3" <?php echo selected( $rule->get_user_filter_type(), '3', true ); ?>><?php esc_html_e( 'Exclude User(s)', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="4" <?php echo selected( $rule->get_user_filter_type(), '4', true ); ?>><?php esc_html_e( 'Include User Role(s)', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="5" <?php echo selected( $rule->get_user_filter_type(), '5', true ); ?>><?php esc_html_e( 'Exclude User Role(s)', 'extra-fees-for-woocommerce' ); ?></option>
			</select>
		</p>
		<p class="efw-multilevel-include-user">
			<label><?php esc_html_e( 'Select User(s) to Include', 'extra-fees-for-woocommerce' ); ?></label>
			<?php
			$include_user_args = array(
				'name'                    => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . '][efw_include_user]',
				'list_type'               => 'customers',
				'exclude_global_variable' => 'yes',
				'action'                  => 'efw_customers_search',
				'placeholder'             => esc_html__( 'Search a User', 'extra-fees-for-woocommerce' ),
				'options'                 => $rule->get_include_user(),
			);
			efw_select2_html( $include_user_args );
			?>
		</p>
		<p class="efw-multilevel-exclude-user">
			<label><?php esc_html_e( 'Select User(s) to Exclude', 'extra-fees-for-woocommerce' ); ?></label>
			<?php
			$exclude_user_args = array(
				'name'                    => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . '][efw_exclude_user]',
				'list_type'               => 'customers',
				'exclude_global_variable' => 'yes',
				'action'                  => 'efw_customers_search',
				'placeholder'             => esc_html__( 'Search a User', 'extra-fees-for-woocommerce' ),
				'options'                 => $rule->get_exclude_user(),
			);
			efw_select2_html( $exclude_user_args );
			?>
		</p>
		<p class="efw-multilevel-include-user-role">
			<label><?php esc_html_e( 'Select User Role(s) to Include', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw_select2" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_include_user_role][]" multiple="multiple">
				<?php
				foreach ( efw_get_user_roles() as $user_role_id => $user_role_name ) :
					$selected = ( in_array( $user_role_id, (array) $rule->get_include_user_role() ) ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $user_role_id ); ?>"<?php echo esc_attr( $selected ); ?>><?php echo esc_html( $user_role_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="efw-multilevel-exclude-user-role">
			<label><?php esc_html_e( 'Select User Role(s) to Exclude', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw_select2" name="efw_multiple_level_fees[<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_exclude_user_role][]" multiple="multiple">
				<?php
				foreach ( efw_get_user_roles() as $user_role_id => $user_role_name ) :
					$selected = ( in_array( $user_role_id, (array) $rule->get_exclude_user_role() ) ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $user_role_id ); ?>"<?php echo esc_attr( $selected ); ?>><?php echo esc_html( $user_role_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label><?php esc_html_e( 'Fee for Product(s)/Categories', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw-product-filter-type-for-multilevel" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_product_filter_type]">
				<option value="1" <?php echo selected( $rule->get_product_filter_type(), '1', true ); ?>><?php esc_html_e( 'All Product(s)', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="2" <?php echo selected( $rule->get_product_filter_type(), '2', true ); ?>><?php esc_html_e( 'Include Product(s)', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="3" <?php echo selected( $rule->get_product_filter_type(), '3', true ); ?>><?php esc_html_e( 'Exclude Product(s)', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="4" <?php echo selected( $rule->get_product_filter_type(), '4', true ); ?>><?php esc_html_e( 'Include Categories', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="5" <?php echo selected( $rule->get_product_filter_type(), '5', true ); ?>><?php esc_html_e( 'Exclude Categories', 'extra-fees-for-woocommerce' ); ?></option>
			</select>
		</p>
		<p class="efw-multilevel-include-product">
			<label><?php esc_html_e( 'Select Product(s) to Include', 'extra-fees-for-woocommerce' ); ?></label>
			<?php
			$include_product_args = array(
				'name'                    => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . ' ][efw_include_product]"',
				'list_type'               => 'products',
				'exclude_global_variable' => 'yes',
				'action'                  => 'efw_product_search',
				'placeholder'             => esc_html__( 'Search a Product', 'extra-fees-for-woocommerce' ),
				'options'                 => $rule->get_include_product(),
			);
			efw_select2_html( $include_product_args );
			?>
		</p>
		<p class="efw-multilevel-exclude-product">
			<label><?php esc_html_e( 'Select Product(s) to Exclude', 'extra-fees-for-woocommerce' ); ?></label>
			<?php
			$exclude_product_args = array(
				'name'                    => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . ' ][efw_exclude_product]"',
				'list_type'               => 'products',
				'exclude_global_variable' => 'yes',
				'action'                  => 'efw_product_search',
				'placeholder'             => esc_html__( 'Search a Product', 'extra-fees-for-woocommerce' ),
				'options'                 => $rule->get_exclude_product(),
			);
			efw_select2_html( $exclude_product_args );
			?>
		</p>
		<p class="efw-multilevel-include-category">
			<label><?php esc_html_e( 'Select Categories to Include', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw_select2" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_include_category][]" multiple="multiple">
				<?php
				foreach ( efw_get_wc_categories() as $category_id => $category_name ) :
					$selected = ( in_array( $category_id, (array) $rule->get_include_category() ) ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $category_id ); ?>"<?php echo esc_attr( $selected ); ?>><?php echo esc_html( $category_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="efw-multilevel-include-additional-product">
			<label><?php esc_html_e( 'Select Additional Product(s) to Include', 'extra-fees-for-woocommerce' ); ?></label>
			<?php
			$include_additional_product_args = array(
				'name'                    => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . '][efw_additional_include_products]',
				'list_type'               => 'products',
				'exclude_global_variable' => 'yes',
				'action'                  => 'efw_product_search',
				'placeholder'             => esc_html__( 'Search a Product', 'extra-fees-for-woocommerce' ),
				'options'                 => $rule->get_additional_include_products(),
			);
			efw_select2_html( $include_additional_product_args );
			?>
		</p>
		<p class="efw-multilevel-exclude-category">
			<label><?php esc_html_e( 'Select Categories to Exclude', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw_select2" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_exclude_product][]" multiple="multiple">
				<?php
				foreach ( efw_get_wc_categories() as $category_id => $category_name ) :
					$selected = ( in_array( $category_id, (array) $rule->get_exclude_category() ) ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $category_id ); ?>"<?php echo esc_attr( $selected ); ?>><?php echo esc_html( $category_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="efw-multilevel-exclude-additional-product">
			<label><?php esc_html_e( 'Select Additional Product(s) to Exclude', 'extra-fees-for-woocommerce' ); ?></label>
			<?php
			$exclude_additional_product_args = array(
				'name'                    => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . '][efw_additional_exclude_product]',
				'list_type'               => 'products',
				'exclude_global_variable' => 'yes',
				'action'                  => 'efw_product_search',
				'placeholder'             => esc_html__( 'Search a Product', 'extra-fees-for-woocommerce' ),
				'options'                 => $rule->get_additional_exclude_products(),
			);
			efw_select2_html( $exclude_additional_product_args );
			?>
		</p>
		<p>
			<label><?php esc_html_e( 'Restrict Fee based on', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw-multilevel-fee-based-on" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_fee_based_on]">
				<option value="1" <?php echo selected( $rule->get_fee_based_on(), '1', true ); ?>><?php esc_html_e( 'Countries', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="2" <?php echo selected( $rule->get_fee_based_on(), '2', true ); ?>><?php esc_html_e( 'State(s)', 'extra-fees-for-woocommerce' ); ?></option>
			</select>
		</p>
		<p class="efw-multilevel-include-country">
			<label><?php esc_html_e( 'Select Countries to Include', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw_select2" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_included_country][]" multiple="multiple">
				<?php
				foreach ( WC()->countries->get_allowed_countries() as $country_code => $country_name ) :
					$selected = ( in_array( $country_code, (array) $rule->get_included_country() ) ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $country_code ); ?>"<?php echo esc_attr( $selected ); ?>><?php echo esc_html( $country_name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="efw-multilevel-include-states">
			<label><?php esc_html_e( 'Select State(s) to Include', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw_select2"  name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_included_states][]" multiple="multiple">
				<?php
				foreach ( efw_get_allowed_states() as $code => $name ) :
					$selected = ( in_array( $code, (array) $rule->get_included_states() ) ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $code ); ?>"<?php echo esc_attr( $selected ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label><?php esc_html_e( 'Date Ranges', 'extra-fees-for-woocommerce' ); ?></label>
			<?php
			$rule_valid_from_date_args = array(
				'name'        => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . '][efw_from_date]',
				'value'       => $rule->get_from_date(),
				'wp_zone'     => false,
				'placeholder' => EFW_Date_Time::get_wp_date_format(),
			);
			efw_get_datepicker_html( $rule_valid_from_date_args );
			esc_html_e( 'To', 'extra-fees-for-woocommerce' );
			$rule_valid_to_date_args = array(
				'name'        => 'efw_multiple_level_fees[' . $rule->get_gateway_id() . '][' . $multilevel_rule_id . '][efw_to_date]',
				'value'       => $rule->get_to_date(),
				'wp_zone'     => false,
				'placeholder' => EFW_Date_Time::get_wp_date_format(),
			);
			efw_get_datepicker_html( $rule_valid_to_date_args );
			?>
		</p>
		<p>
			<label><?php esc_html_e( 'Weekday(s)', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw_select2" multiple="multiple" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_weekdays][]">
				<?php foreach ( efw_get_weekdays_options() as $specific_weekdays_id => $specific_weekdays_name ) : ?>
					<option value="<?php echo esc_attr( $specific_weekdays_id ) ; ?>" <?php echo in_array( $specific_weekdays_id , (array) $rule->get_weekdays() ) ? 'selected="selected"' : '' ; ?>><?php echo esc_html( $specific_weekdays_name ) ; ?></option>
				<?php endforeach ; ?>
			</select>
		</p>
		<p>
			<label><?php esc_html_e( 'Fee Text', 'extra-fees-for-woocommerce' ); ?><span class="required">*</span></label>
			<input name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_fee_text]" value="<?php echo esc_html( $rule->get_fee_text() ); ?>"/>
		</p>
		<p>
			<label><?php esc_html_e( 'Fee Text Description', 'extra-fees-for-woocommerce' ); ?></label>
			<textarea name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_fee_description]" style="margin-left:10px;"><?php echo wp_kses_post( $rule->get_fee_description() ); ?></textarea>
		</p>
		<p>
			<label><?php esc_html_e( 'Tax Class', 'extra-fees-for-woocommerce' ); ?></label>
			<select name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_tax_class]">
				<?php foreach ( efw_get_fee_tax_classes() as $tax_class_id => $tax_class_name ) : ?>
						<option value="<?php echo esc_attr( $tax_class_id ); ?>" <?php echo selected( $rule->get_tax_class(), $tax_class_id, true ); ?>><?php echo esc_html( $tax_class_name ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php echo wc_help_tip( 'Select the tax which should be used for calculating the fee' ); ?>
		</p>
		<p>
			<label><?php esc_html_e( 'Fee Type', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw-multilevel-fee-type" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_fee_type]">
				<option value="1" <?php echo selected( $rule->get_fee_type(), '1', true ); ?>><?php esc_html_e( 'Fixed Fee', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="2" <?php echo selected( $rule->get_fee_type(), '2', true ); ?>><?php esc_html_e( 'Percentage', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="3" <?php echo selected( $rule->get_fee_type(), '3', true ); ?>><?php esc_html_e( 'Fixed + Percentage', 'extra-fees-for-woocommerce' ); ?></option>
			</select>
		</p>
		<p class="efw-multilevel-percentage-calc-based-on">
			<label><?php esc_html_e( 'Percentage calculation based on', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw-multilevel-percentage-calculation-based-on" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_percentage_type]">
				<option value="1" <?php echo selected( $rule->get_percentage_type(), '1', true ); ?>><?php esc_html_e( 'Cart Subtotal', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="2" <?php echo selected( $rule->get_percentage_type(), '2', true ); ?>><?php esc_html_e( 'Order Total', 'extra-fees-for-woocommerce' ); ?></option>
			</select>
		</p>
		<p class="efw-multilevel-percentage-fee-type">
			<label><?php esc_html_e( 'Percentage Type', 'extra-fees-for-woocommerce' ); ?></label>
			<select class="efw-multilevel-percentage-fee-type" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_percentage_fee_type]">
				<option value="1" <?php echo selected( $rule->get_percentage_fee_type(), '1', true ); ?>><?php esc_html_e( 'Add Percentage to Order Total', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="2" <?php echo selected( $rule->get_percentage_fee_type(), '2', true ); ?>><?php esc_html_e( 'Include Percentage to Order Total', 'extra-fees-for-woocommerce' ); ?></option>
			</select>
			<?php echo wc_help_tip( '<b>Add Percentage to Total:</b> The fee will be calculated based on the Order Total without the fee included.<br><b>Include Percentage to Total:</b> The fee will be calculated based on the Order Total with the fee included.' ); ?>
		</p>
		<p class="efw-multilevel-fixed-fee">
			<label><?php esc_html_e( 'Fixed Fee Value', 'extra-fees-for-woocommerce' ); ?><span class="required">*</span></label>
			<input type="number" min="0" step="any" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_fixed_value]" value="<?php echo esc_html( $rule->get_fixed_value() ); ?>"/>
		</p>
		<p class="efw-multilevel-percent-cart-subtotal">
			<label><?php esc_html_e( 'Fee Value in %', 'extra-fees-for-woocommerce' ); ?><span class="required">*</span></label>
			<input type="number" min="0" step="any" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_percent_value]" value="<?php echo esc_html( $rule->get_percent_value() ); ?>"/>
		</p>
		<p class="efw-multilevel-add-fixed-fee">
			<label><?php esc_html_e( 'Add Fixed Fee', 'extra-fees-for-woocommerce' ); ?></label>
			<select name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_add_fixed_fee]">
				<option value="1" <?php echo selected( $rule->get_add_fixed_fee(), '1', true ); ?>><?php esc_html_e( 'After Percentage Value is Calculated', 'extra-fees-for-woocommerce' ); ?></option>
				<option value="2" <?php echo selected( $rule->get_add_fixed_fee(), '2', true ); ?>><?php esc_html_e( 'Before Percentage Value is Calculated', 'extra-fees-for-woocommerce' ); ?></option>
			</select>
		</p>
		<p class="efw-multilevel-minimum-fee">
			<label><?php esc_html_e( 'Minimum Fee', 'extra-fees-for-woocommerce' ); ?></label>
			<input type="number" min="0" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_min_fee]" value="<?php echo esc_html( $rule->get_min_fee() ); ?>"/>
			<?php echo wc_help_tip( 'The fee value configured in this field will consider when the fee is calculated less than the minimum fee' ); ?>
		</p>
		<p class="efw-multilevel-maximum-fee">
			<label><?php esc_html_e( 'Maximum Fee', 'extra-fees-for-woocommerce' ); ?></label>
			<input type="number" min="0" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_max_fee]" value="<?php echo esc_html( $rule->get_max_fee() ); ?>"/>
			<?php echo wc_help_tip( 'The fee value configured in this field will consider when the fee is calculated more than the maximum fee' ); ?>
		</p>
		<p class="efw-multilevel-minimum-cart-subtotal">
			<label><?php esc_html_e( 'Minimum Cart Subtotal to Add Fee', 'extra-fees-for-woocommerce' ); ?></label>
			<input type="number" min="0" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_min_sub_total]" value="<?php echo esc_html( $rule->get_min_sub_total() ); ?>" step="any"/>
		</p>
		<p class="efw-multilevel-maximum-cart-subtotal">
			<label><?php esc_html_e( ' Maximum Cart Subtotal to Add Fee', 'extra-fees-for-woocommerce' ); ?></label>
			<input type="number" min="0" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_max_sub_total]" value="<?php echo esc_html( $rule->get_max_sub_total() ); ?>" step="any"/>
		</p>
		<p class="efw-multilevel-minimum-order-subtotal">
			<label><?php esc_html_e( 'Minimum Order Total to Add Fee', 'extra-fees-for-woocommerce' ); ?></label>
			<input type="number" min="0" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_min_order_total]" value="<?php echo esc_html( $rule->get_min_order_total() ); ?>" step="any"/>
		</p>
		<p class="efw-multilevel-maximum-order-subtotal">
			<label><?php esc_html_e( ' Maximum Order Total to Add Fee', 'extra-fees-for-woocommerce' ); ?></label>
			<input type="number" min="0" name="efw_multiple_level_fees[<?php echo esc_attr( $rule->get_gateway_id() ); ?>][<?php echo esc_attr( $multilevel_rule_id ); ?>][efw_max_order_total]" value="<?php echo esc_html( $rule->get_max_sub_total() ); ?>" step="any"/>
		</p>
	</div>
</div>
<?php
