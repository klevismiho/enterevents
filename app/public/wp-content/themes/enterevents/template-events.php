<?php 
/**
 * Template Name: Events
 */

get_header(); 


// Query for latest products (events) - ONLY PRODUCTS WITH A FEATURED IMAGE
$args = array(
  'post_type' => 'product',
  'post_status' => 'publish',
  'posts_per_page' => 16,
  'orderby' => 'date',
  'order' => 'DESC',
  'meta_query' => array(
    array(
      'key' => '_thumbnail_id',
      'compare' => 'EXISTS'
    )
  )
);

$products = new WP_Query($args);

if (!$products->have_posts()) {
  echo '<p>No events (products) found with a featured image.</p>';
  return; // Stop if no products are found
}
?>

<section class="section-latest-events upcoming_cards" data-category="" data-ppp="<?php echo esc_attr($posts_per_page); ?>">

  <div class="container">

    <h2>Upcoming Events</h2>

    <div class="events-grid">
      <?php
      // Start the loop
      while ($products->have_posts()) :
        $products->the_post();
        global $product;
        if ($product->get_type() !== 'external' && !$product->is_in_stock()) {
          continue; // Skip to the next product in the loop
        }
      ?>

        <a class="event-item" href="<?php echo esc_url(get_permalink()); ?>">
          <div class="enter_events_upcoming_card">

            <?php
            // Product image
            // Since we queried for it, we know it exists, but we check anyway
            if (has_post_thumbnail()) {
              the_post_thumbnail('full', array('class' => 'upcoming_card_img wp-post-image'));
            }
            ?>

            <div class="upcoming_card_icons">
              <?php if ($show_sale_badge === 'yes' && $product->is_on_sale()) : ?>
                <span class="sale-badge">Sale!</span>
              <?php endif; ?>
            </div>

            <div class="upcoming_card_content">

              <div class="upcoming_date">
                <?php
                // Get FooEvents start date
                $event_start_date = get_post_meta(get_the_ID(), 'WooCommerceEventsDate', true);
                $month = date('M'); // Default to current month
                $day = date('d');   // Default to current day

                if (!empty($event_start_date)) {
                  // FooEvents stores date in Y-m-d format
                  $date_obj = DateTime::createFromFormat('Y-m-d', $event_start_date);
                  if ($date_obj) {
                    $month = $date_obj->format('M');
                    $day = $date_obj->format('d');
                  } else {
                    // Fallback if date format is different
                    $month = date('M', strtotime($event_start_date));
                    $day = date('d', strtotime($event_start_date));
                  }
                }
                ?>
                <span class="month"><?php echo esc_html($month); ?></span>
                <span class="day"><?php echo esc_html($day); ?></span>
              </div>

              <div class="upcoming_card_text">

                <div class="upcoming_card_title">
                  <p><?php the_title(); ?></p>
                </div>

              </div>
            </div>
          </div>
        </a>

      <?php endwhile; ?>
    </div>

  </div>

</section>

<?php
// Restore original Post Data
wp_reset_postdata();
?>

<?php get_footer(); ?>