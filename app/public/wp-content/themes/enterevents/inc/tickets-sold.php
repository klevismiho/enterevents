<?php

// Add menu item
add_filter('woocommerce_account_menu_items', 'add_custom_link_to_my_account', 25);
function add_custom_link_to_my_account($items)
{
    // Remove logout
    unset($items['customer-logout']);

    // Add tickets sold
    $items['tickets-sold'] = 'Tickets Sold';

    return $items;
}

// Add endpoint
add_action('init', 'add_custom_rewrite_links');
function add_custom_rewrite_links()
{
    add_rewrite_endpoint('tickets-sold', EP_ROOT | EP_PAGES);
}

// Content with your logic
add_action('woocommerce_account_tickets-sold_endpoint', 'add_tickets_sold_content');
function add_tickets_sold_content()
{

    if (!is_user_logged_in()) {
        echo '<p>Please log in to view your tickets sold.</p>';
        return;
    }

    try {
        $user = wp_get_current_user();
        $user_products = get_user_meta($user->ID, 'user_products', true);

        if (empty($user_products)) {
            echo '<p>No products assigned to your account yet.</p>';
            return;
        }

        if (is_string($user_products)) {
            $user_products = unserialize($user_products);
        }

        if (!is_array($user_products)) {
            echo '<p>Invalid product data format.</p>';
            return;
        }

        echo '<h2 style="margin-top:0;">Tickets sold:</h2>';
        echo '<ul class="tickects-sold-list" style="list-style-type:none;padding:0;">';

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
                // Check if product is variable
                if ($product->is_type('variable')) {
                    $posts = $query->get_posts();
                    $children = [];

                    foreach ($posts as $post) {
                        $var_id = get_post_meta($post->ID, 'WooCommerceEventsVariationID', true);
                        $attributes = get_post_meta($post->ID, 'WooCommerceEventsVariations', true);

                        if (!array_key_exists($var_id, $children)) {
                            $var_title = get_post_meta($var_id, 'variation_title', true);
                            $children[$var_id] = [
                                'date' => isset($attributes['attribute_date']) ? $attributes['attribute_date'] : '',
                                'var_title' => $var_title,
                                'count' => 1
                            ];
                        } else {
                            $children[$var_id]['count'] += 1;
                        }
                    }

                    if ($children) {
                        foreach ($children as $child) {
                            $variation_count = $child['count'];
                            $total_tickets += $variation_count;
                            echo '<li style="margin-bottom:15px;font-size:18px;">'
                                . esc_html($product->get_title())
                                . ' ( ' . esc_html($child['var_title'])
                                . ' - ' . esc_html($child['date'])
                                . ' ): ' . $variation_count
                                . '</li>';
                        }
                    }
                } else {
                    // Simple product
                    $tickets_count = $query->found_posts;
                    $total_tickets += $tickets_count;
                    echo '<li style="margin-bottom:15px;font-size:18px;">'
                        . esc_html($product->get_title())
                        . ': ' . $tickets_count
                        . '</li>';
                }
            }

            wp_reset_postdata();
        }

        echo '</ul>';
    } catch (Exception $e) {
        echo '<p>Error loading ticket data. Please contact support.</p>';
        error_log('Tickets Sold Error: ' . $e->getMessage());
    }
}
