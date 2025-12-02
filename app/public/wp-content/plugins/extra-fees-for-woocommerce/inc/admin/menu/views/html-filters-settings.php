<?php
/**
 * Filter Settings
 *
 * @package Extra Fees for WooCommerce/Admin/Views
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="efw-filter <?php echo esc_attr($class_name); ?>">
	<?php 
	if ($show_date_filter) : 
		?>
		<div class="efw-date-filters">
			<select name="efw_filter" class="efw-filter-type">
				<?php foreach ($date_filter as $option_key => $option_value) : ?>
					<option value="<?php echo esc_attr($option_key); ?>" <?php selected($selected_filter, $option_key); ?>><?php echo esc_html( $option_value ); ?></option>
				<?php endforeach; ?>
			</select>
			<div class="efw-custom-date-range">
				<?php
				efw_get_datepicker_html( array(
					'name'        => 'efw_from_date',
					'wp_zone'     => false,
					'placeholder' => EFW_Date_Time::get_wp_datetime_format(),
					'value' => $from_date,
				) ) ;
				esc_html_e('To', 'extra-fees-for-woocommerce');
				efw_get_datepicker_html( array(
					'name'        => 'efw_to_date',
					'wp_zone'     => false,
					'placeholder' => EFW_Date_Time::get_wp_datetime_format(),
					'value' => $to_date,
				) ) ;
				?>
			</div>
			<input type="submit" class="button-primary" name="efw_filter_button" value="<?php esc_html_e( 'Filter', 'affiliate-suite-for-woocommerce' ); ?>"/>
		</div>
		<?php 
	endif; 
	if ($pagination_filter) : 
		?>
		<div class="efw-pagination-filters">
			<label for="efw_item_per_page_input"><?php esc_html_e( 'Pagination Size', 'affiliate-suite-for-woocommerce' ); ?>:</label>
			<input type="number" min="0" name="efw_item_per_page_input" value="<?php echo esc_attr( $post_per_page ); ?>" min="1">
			<input type="hidden" name="efw_post_type" value="<?php esc_attr_e($post_type); ?>" />
			<input type="submit" id="efw_item_per_page_submit" class="button-primary action" value="<?php esc_html_e( 'Apply', 'affiliate-suite-for-woocommerce' ); ?> ">
		</div>
	<?php endif; ?>
</div>
<?php
