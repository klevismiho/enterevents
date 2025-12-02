<?php

require_once('inc/woocommerce.php');
require_once('inc/codereadr.php');
require_once('inc/tickets-sold.php');

add_theme_support( 'menus' );
add_theme_support( 'post-thumbnails' );

add_action( 'init', 'register_my_menus' );

function register_my_menus() {
    register_nav_menus(
        array(
            'primary-menu' => __( 'Primary Menu' ),
            'secondary-menu' => __( 'Secondary Menu' )
        )
    );
}


add_action( 'init', 'remove_wc_gallery_lightbox', 100 );
function remove_wc_gallery_lightbox() { 
    remove_theme_support( 'wc-product-gallery-lightbox' );
}

function ee_scripts() {
    wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), true);
    wp_enqueue_script( 'ee-scripts', get_stylesheet_directory_uri(). '/js/app.js', array(), '1.0.2', true );
    wp_enqueue_style( 'google-fonts-ee', 'https://fonts.googleapis.com/css2?family=Inconsolata:wght@300;400;700&display=swap' );
    wp_enqueue_style( 'ee-style', get_stylesheet_directory_uri(). '/style.css', [], '1.0.8' );

    if(is_product()) {
        wp_enqueue_script( 'lity', get_stylesheet_directory_uri(). '/js/lity.min.js', array('jquery'), '', true );
        wp_enqueue_style( 'lity', get_stylesheet_directory_uri(). '/css/lity.min.css' );

    }
    
}
add_action( 'wp_enqueue_scripts', 'ee_scripts' );


/**
 * Auto Complete all WooCommerce orders.
 * Add to theme functions.php file
*/

add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
function custom_woocommerce_auto_complete_order( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    // Skip these payment methods
    $excluded_methods = array( 'cod', 'pok' );

    if ( ! in_array( $order->get_payment_method(), $excluded_methods, true ) ) {
        $order->update_status( 'completed' );
    }
}

add_filter( 'woocommerce_countries',  'enterevents_add_kosovo_country' );
function enterevents_add_kosovo_country( $countries ) {
    $new_countries = array(
        'KOSOVO'  => __( 'Kosovo', 'woocommerce' ),
    );
    return array_merge( $countries, $new_countries );
}



add_filter( 'woocommerce_single_product_zoom_enabled', '__return_false' );

remove_theme_support( 'wc-product-gallery-lightbox' );


add_action( 'woocommerce_product_after_variable_attributes', 'add_to_variations_metabox', 4, 3 );
add_action( 'woocommerce_save_product_variation', 'save_product_variation', 20, 2 );

/*
 * Add new inputs to each variation
 *
 * @param string $loop
 * @param array $variation_data
 * @return print HTML
 */
function add_to_variations_metabox( $loop, $variation_data, $variation ){

    $custom = get_post_meta( $variation->ID, 'variation_title', true ); 
    woocommerce_wp_text_input(
        array(
            'id'            => 'variation_title[' . $loop . ']',
            'label'         => 'Title',
            'wrapper_class' => 'form-row',
            'placeholder'   => 'Type here...',
            'desc_tip'      => 'true',
            'description'   => 'Add title to variation',
            'value'         => get_post_meta( $variation->ID, 'variation_title', true )
        )
    );
    
    woocommerce_wp_checkbox( array(
        'id'        => 'coming_soon[' . $loop . ']',
        'desc'      => __('Display coming soon instead of Buy now', 'woocommerce'),
        'label'     => __('Set coming soon status', 'woocommerce'),
        'desc_tip'  => 'true',
        'value'         => get_post_meta( $variation->ID, 'coming_soon', true ),
    ));

}

/*
 * Save extra meta info for variable products
 *
 * @param int $variation_id
 * @param int $i
 * return void
 */
function save_product_variation( $variation_id, $loop ){

    // save custom data
    $title_field = ! empty( $_POST[ 'variation_title' ][ $loop ] ) ? $_POST[ 'variation_title' ][ $loop ] : '';
    update_post_meta( $variation_id, 'variation_title', sanitize_text_field( $title_field ) );
    
    $checkbox_field = ! empty( $_POST[ 'coming_soon' ][ $loop ] ) ? 'yes' : 'no';
    update_post_meta( $variation_id, 'coming_soon', $checkbox_field );

}

add_filter( 'woocommerce_available_variation', function( $variation ) {

    $variation[ 'variation_title' ] = get_post_meta( $variation[ 'variation_id' ], 'variation_title', true );
    $single_variation = new WC_Product_Variation($variation[ 'variation_id' ]);
    $regular_price = $single_variation->get_price_html();
    $variation[ 'price_html' ] = $regular_price;
    return $variation;

} );

add_shortcode( 'card_date', 'product_card_date' );
function product_card_date() {
    $getDate = get_post_meta(get_the_ID(), 'WooCommerceEventsDate', true );
    $timestamp = strtotime($getDate);
    $date = new DateTime();
    $date->setTimestamp($timestamp);
    $day = $date->format('d');
    $month = date_i18n('M', $date->getTimestamp());
    
    ob_start();
    ?> 
    <div class="upcoming_date">
        <span class="month"><?php echo $month; ?></span>
        <span class="day"><?php echo $day; ?></span>
    </div>
    <?php
    return ob_get_clean();
}


add_shortcode( 'event_starting', 'get_event_starting_time' );
function get_event_starting_time() {
    $event_hour = get_post_meta(get_the_ID(), 'WooCommerceEventsHour', true );
    $event_min = get_post_meta(get_the_ID(), 'WooCommerceEventsMinutes', true );
    
    ob_start();
    ?> 
    <div class="event_time">
        <?php echo $event_hour . ':' . $event_min; ?>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode( 'product_short_description', 'product_short_description' );
function product_short_description() {
    global $post;
    $product = wc_get_product($post->ID);
    $description = $product->get_description();
    $short_description = $product->get_short_description();
    ob_start();
    ?>
    <div class="product_short_description">
        <div class="short_desc">
            <?php echo $short_description; echo $description ? ' <span class="open_long">' . __('...see more', 'woocommerce') . '</span>' : '';  ?>
            <?php if($description): ?>
                <span class="full_description" style="display: none;"><?php echo $description; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php 
}

function custom_my_account_menu_items( $items ) {
    $user = wp_get_current_user();
    if ( in_array( 'shop_manager', (array) $user->roles ) ) {
        unset($items['downloads']);
        unset($items['edit-address']);
        unset($items['fooevents-tickets']);
        unset($items['affiliate-dashboard']);
        
    } else if( in_array( 'subscriber', (array) $user->roles ) ) {
        unset($items['downloads']);
        unset($items['edit-address']);
        unset($items['fooevents-tickets']);
        unset($items['affiliate-dashboard']);
        unset($items['tickets-sold']);

    }
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items' );
?>



<?php
/**
 * Custom shortcode to display 9 latest WooCommerce products
 * Usage: [enter_latest_products] or [enter_latest_products count="6"] to customize number
 */

function enter_display_latest_products($atts) {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '<p>WooCommerce is not active.</p>';
    }
    
    // Set default attributes
    $atts = shortcode_atts(array(
        'count' => 21,
        'show_price' => 'yes',
        'show_rating' => 'yes',
        'show_sale_badge' => 'yes'
    ), $atts, 'enter_latest_products');
    
    // Query for latest products - only in stock and external products
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['count']),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $products = new WP_Query($args);
    
    if (!$products->have_posts()) {
        return '<p>No products found.</p>';
    }
    
    $output = '<div class="upcoming_cards" data-category="" data-ppp="' . intval($atts['count']) . '">';
    
    while ($products->have_posts()) {
        $products->the_post();
        global $product;
        
        // Skip products that are out of stock (but allow external products)
        if ($product->get_type() !== 'external' && !$product->is_in_stock()) {
            continue;
        }
        
        $output .= '<a href="' . get_permalink() . '">';
        $output .= '<div class="enter_events_upcoming_card">';
        
        // Product image
        if (has_post_thumbnail()) {
            $output .= get_the_post_thumbnail(get_the_ID(), 'full', array('class' => 'upcoming_card_img wp-post-image'));
        }
        
        // Icons container (for sale badge or other icons)
        $output .= '<div class="upcoming_card_icons">';
        if ($atts['show_sale_badge'] === 'yes' && $product->is_on_sale()) {
            $output .= '<span class="sale-badge">Sale!</span>';
        }
        $output .= '</div>';
        
        // Card content
        $output .= '<div class="upcoming_card_content">';
        
        // Date section - Get event start date from FooEvents
        $output .= '<div class="upcoming_date">';
        
        // Get FooEvents start date
        $event_start_date = get_post_meta(get_the_ID(), 'WooCommerceEventsDate', true);
        
        if (!empty($event_start_date)) {
            // FooEvents stores date in Y-m-d format
            $date_obj = DateTime::createFromFormat('Y-m-d', $event_start_date);
            if ($date_obj) {
                $output .= '<span class="month">' . $date_obj->format('M') . '</span>';
                $output .= '<span class="day">' . $date_obj->format('d') . '</span>';
            } else {
                // Fallback if date format is different
                $output .= '<span class="month">' . date('M', strtotime($event_start_date)) . '</span>';
                $output .= '<span class="day">' . date('d', strtotime($event_start_date)) . '</span>';
            }
        } else {
            // Fallback to current date if no event date is set
            $output .= '<span class="month">' . date('M') . '</span>';
            $output .= '<span class="day">' . date('d') . '</span>';
        }
        
        $output .= '</div>';
        
        // Card text content
        $output .= '<div class="upcoming_card_text">';
        
        // Product title
        $output .= '<div class="upcoming_card_title">';
        $output .= '<p>' . get_the_title() . '</p>';
        $output .= '</div>';
        
        // Product description/excerpt 
        $output .= '<div class="upcoming_card_desc">';
        $excerpt = get_the_excerpt();
        if (empty($excerpt)) {
            $excerpt = wp_trim_words(get_the_content(), 15, '...');
        }
        $output .= $excerpt;
        $output .= '</div>';
        
        $output .= '</div>'; // end upcoming_card_text
        $output .= '</div>'; // end upcoming_card_content
        $output .= '</div>'; // end enter_events_upcoming_card
        $output .= '</a>';
    }
    
    $output .= '</div>';
    
    wp_reset_postdata();
    
    return $output;
}

// Register the shortcode
add_shortcode('enter_latest_products', 'enter_display_latest_products');


/* 
* Show only posts in search results
*/
function show_only_posts_in_search( $query ) {
  if ( $query->is_search() && $query->is_main_query() && !is_admin() ) {
    $query->set( 'post_type', 'post' );
  }
}
add_action( 'pre_get_posts', 'show_only_posts_in_search' );


// Remove FooEvents Tickets from My Account menu
add_filter( 'woocommerce_account_menu_items', 'remove_fooevents_from_my_account', 21 );
function remove_fooevents_from_my_account( $items ) {
    unset( $items['fooevents-tickets'] );
    return $items;
}


?>