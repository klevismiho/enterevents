<?php

// Remove Related Products from Single Product Page
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Reposition the product title
function move_product_title() {
    // Remove the title from its default location
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    
    // Add the title to the beginning of the summary
    add_action('woocommerce_single_product_summary', 'custom_product_title', 1);
}
add_action('init', 'move_product_title');

// Custom function to display the title
function custom_product_title() {
    the_title('<h1 class="product_title entry-title">', '</h1>');
}



// Remove product meta (SKU, categories, tags) from single product page
function remove_product_meta() {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
}
add_action('init', 'remove_product_meta');



// Remove product tabs from single product page
function remove_product_tabs() {
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
}
add_action('init', 'remove_product_tabs');



// Add product description after Add to Cart button
function add_description_after_add_to_cart() {
    global $product;
    
    // Get the product description
    $description = $product->get_description();
    
    // Only display if there is a description
    if (!empty($description)) {
        echo '<div class="product-description-under-cart">';
        echo '<h3>Description</h3>'; // Optional heading
        echo '<div class="woocommerce-product-details__description">';
        echo wpautop($description); // Format with paragraphs
        echo '</div>';
        echo '</div>';
    }
}
// Hook after Add to Cart (priority 30) with priority 35
add_action('woocommerce_single_product_summary', 'add_description_after_add_to_cart', 35);