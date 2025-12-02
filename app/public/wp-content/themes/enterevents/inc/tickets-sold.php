<?php

// Add menu item
add_filter( 'woocommerce_account_menu_items', 'add_custom_link_to_my_account', 25 );
function add_custom_link_to_my_account( $items ) {
    // Remove logout
    unset($items['customer-logout']);
    
    // Add tickets sold
    $items['tickets-sold'] = 'Tickets Sold';
    
    return $items;
}

// Add endpoint
add_action( 'init', 'add_custom_rewrite_links' );
function add_custom_rewrite_links() {
    add_rewrite_endpoint( 'tickets-sold', EP_ROOT | EP_PAGES );
}

// Content with your logic
add_action( 'woocommerce_account_tickets-sold_endpoint', 'add_tickets_sold_content' );
function add_tickets_sold_content() {
    
    if (!is_user_logged_in()) {
        echo '<p>Please log in to view your tickets sold.</p>';
        echo '</div>';
        return;
    }
    
    try {
        $user = wp_get_current_user();
        $user_products = get_user_meta($user->ID, 'user_products', true);
        
        if (empty($user_products)) {
            echo '<p>No products assigned to your account yet.</p>';
            echo '</div>';
            return;
        }
        
        if (is_string($user_products)) {
            $user_products = unserialize($user_products);
        }
        
        if (!is_array($user_products)) {
            echo '<p>Invalid product data format.</p>';
            echo '</div>';
            return;
        }
        
        $total_tickets = 0;
        
        foreach ($user_products as $product_id) {
            
            if (!is_numeric($product_id)) continue;
            
            $query = new WP_Query(array(
                'post_type' => 'event_magic_tickets',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'WooCommerceEventsProductID',
                        'value' => $product_id,
                        'compare' => 'LIKE'
                    )
                )
            ));
            
            $product = wc_get_product($product_id);
            
            if ($product) {
                $tickets_count = $query->found_posts;
                $total_tickets += $tickets_count;
                echo '<h4 style="margin-bottom:15px;font-size:24px;">' . esc_html($product->get_title()) . ': ' . $tickets_count . '</h4>';
            }
            
            wp_reset_postdata();
        }
        
        echo '<hr style="margin: 20px 0;">';
        echo '<h3 style="font-size:28px;">Total Tickets Sold: ' . $total_tickets . '</h3>';
        
    } catch (Exception $e) {
        echo '<p>Error loading ticket data. Please contact support.</p>';
        error_log('Tickets Sold Error: ' . $e->getMessage());
    }
    
}