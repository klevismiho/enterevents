
<?php
$user = wp_get_current_user();

$user_meta = get_user_meta($user->ID);

$user_products = unserialize($user_meta['user_products'][0]);

echo '<div class="container">';

echo '<h2 style="font-size:62px;display:block;">Tickets sold:</h2>';

if (is_user_logged_in()) {
    echo '<ul class="tickects-sold-list">';

    foreach ($user_products as $product_id) {

        $query = new WP_Query(
            array(
                'post_type' => 'event_magic_tickets',
                'posts_per_page' => -1,
                'meta_query'  => array(
                    'relation'    => 'OR',
                    array(
                        'key'   => 'WooCommerceEventsProductID',
                        'value'   => $product_id,
                        'compare' => 'LIKE'
                    )
                )
            )
        );

        $product = wc_get_product($product_id);

        // $meta = get_post_meta($query->get_posts()[0]->ID);
        if( $product->is_type('variable') ){
            // echo '<li>' . $product->get_title() . ': ' . $query->found_posts . '</li>';
            $posts = $query->get_posts();
            $children = [];
            foreach($posts as $post):
                $var_id = get_post_meta($post->ID, 'WooCommerceEventsVariationID', true);
                $attributes = get_post_meta($post->ID, 'WooCommerceEventsVariations', true);
                if(!array_key_exists($var_id, $children)) {
                    $var_title = get_post_meta( $var_id, 'variation_title', true );
                    $children[$var_id] = [
                        'date' => $attributes['attribute_date'],
                        'var_title' => $var_title,
                        'count' => 1
                    ];
                } else {
                    $children[$var_id]['count'] += 1;
                }
            endforeach;
            if($children) {
                foreach($children as $child) {
                    echo '<li>' . $product->get_title() . '( ' . $child['var_title'] . ' - ' . $child['date'] . ' ): ' . $child['count'] . '</li>';
                }
            }
            // echo '<pre>';
            // var_dump($children);
            // echo '</pre>';
        } else {
            echo '<li>' . $product->get_title() . ': ' . $query->found_posts . '</li>';
        }


        wp_reset_postdata();
    }

    echo '</ul';
}

echo '</div>';
