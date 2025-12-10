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

// Add product description after the title
function add_description_after_title() {
    global $product;
    
    // Get the product description
    $description = $product->get_description();
    
    // Only display if there is a description
    if (!empty($description)) {
        echo '<div class="product-description-after-title">';
        echo '<div class="woocommerce-product-details__description">';
        echo wpautop($description); // Format with paragraphs
        echo '</div>';
        echo '</div>';
    }
}
// Hook after the title (priority 1) with priority 2
add_action('woocommerce_single_product_summary', 'add_description_after_title', 2);


// Remove the add-to-cart form from entry summary for variable products only
function move_variable_products_outside_summary() {
    global $product;
    
    // Check if we're on a single product page and the product is variable
    if (is_product() && $product && $product->is_type('variable')) {
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        
        // Add it after the summary section
        add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_add_to_cart', 5);
    }
}
add_action('woocommerce_before_single_product_summary', 'move_variable_products_outside_summary');


// Replace add-to-cart form with custom button for variable products
function replace_variable_product_add_to_cart() {
    global $product;
    
    // Check if we're on a single product page and the product is variable
    if (is_product() && $product && $product->is_type('variable')) {
        // Remove the default add-to-cart form
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        
        // Add our custom button instead
        add_action('woocommerce_single_product_summary', 'custom_variable_product_button', 30);
    }
}
add_action('woocommerce_before_single_product_summary', 'replace_variable_product_add_to_cart');

// Custom button function
function custom_variable_product_button() {
    echo '<a href="#buy-variations" class="buy-tickets-button">Buy Tickets</a>';
}