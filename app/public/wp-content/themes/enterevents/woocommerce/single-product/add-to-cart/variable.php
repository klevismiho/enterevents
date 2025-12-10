<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 6.1.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
$all_variations = $product->get_children();

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form id="buy-variations" class="variations_form cart custom" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) :  ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0" role="presentation">
			<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></th>
						<td class="value">
							<?php
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'attribute' => $attribute_name,
										'product'   => $product,
									)
								);
								echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php  foreach ( $all_variations as $var_id ) :
		// var_dump($var); 
// 			$var_id = $var['variation_id'];
			$single_variation = new WC_Product_Variation($var_id);
			$var_title = get_post_meta( $var_id, 'variation_title', true );
			$regular_price = $single_variation->get_price_html();
			$var_stock = $single_variation->get_stock_status();
			$all_attr = '';
			$coming_soon_var = get_post_meta( $var_id, 'coming_soon', true );
		?>
		<?php foreach($single_variation->attributes as $var_key => $var_val): 
			$all_attr .= 'data-attribute_' . $var_key . '="' . $var_val . '" ';
			?>
		<?php endforeach; ?>
			<div class="event_variation_row">
				<div class="event_varition_text">
					<h2><?php echo $var_title . ' - ' . $regular_price; ?></h2>
					<span><?php echo $single_variation->get_description(); ?></span>
					<?php if($var_quantity): ?>
						<p><?php printf(__('%d Stock', 'woocommerce'), $var_quantity); ?></p>
					<?php endif; ?>
				</div>
				<?php if(!get_field('coming_soon', $product->get_id())): ?>
					<?php if($var_stock == 'instock'): ?>
						<?php if($coming_soon_var == 'yes'): ?>
							<div class="event_variation_coming_soon">
								<a href="javascript:void(0);" ><?php echo __('Coming soon', 'woocommerce'); ?></a>
							</div>
						<?php else: ?>
							<div class="event_variation_button">
								<a href="#variation_add_to_cart" <?php echo $all_attr ?> data-id="<?php echo $var_id; ?>"><?php echo __('Buy Ticket', 'woocommerce'); ?></a>
							</div>
						<?php endif; ?>
					<?php else: ?>
						<div class="out_of_stock">
							<a href="javascript:void(0);" ><?php echo __('Out of stock', 'woocommerce'); ?></a>
						</div>
					<?php endif; ?>
				<?php else: ?>
				<div class="event_variation_coming_soon">
					<a href="#" ><?php echo __('Coming soon', 'woocommerce'); ?></a>
				</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_backdrop">
			<div class="single_variation_wrap">
				<div class="add_to_cart_close">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
					</svg>
				</div>
				<h3><?php _e('Choose quantity', 'woocommerce'); ?></h3>
				<?php
					/**
					 * Hook: woocommerce_before_single_variation.
					 */
					do_action( 'woocommerce_before_single_variation' );
	
					/**
					 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
					 *
					 * @since 2.4.0
					 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
					 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
					 */
					do_action( 'woocommerce_single_variation' );
	
					/**
					 * Hook: woocommerce_after_single_variation.
					 */
					do_action( 'woocommerce_after_single_variation' );
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>
<script>
	jQuery(document).ready(function($) {
		$('.event_variation_row .event_variation_button a').each(function() {
			var that = $(this)
			$(this).on('click', function(e) {
				e.preventDefault();
				var datas = that.data();
				console.log(datas['id']);
				
				for (const [key, value] of Object.entries(datas)) {
					if(key == 'id') {continue;}
					// console.log(`${key}: ${value}`);
					$(`[name="${key}"]`).val(value).trigger('change')
				}

				$('.single_variation_backdrop').toggleClass('show')
			})
		})
		$('.add_to_cart_close').on('click', function() {
			$('.single_variation_backdrop').removeClass('show')
		})
	})
</script>
<?php
do_action( 'woocommerce_after_add_to_cart_form' );