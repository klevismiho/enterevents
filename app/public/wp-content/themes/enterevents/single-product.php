<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post();
    // Get the post/product ID
    $post_id = get_the_ID();
    // Check if this is a WooCommerce product with FooEvents data
    $is_event = get_post_meta($post_id, 'WooCommerceEventsEvent', true) === 'Event';
?>
    <main class="main-single-product">
      <article>
        <div class="container">
          <?php the_content(); ?>
        </div>
        <div class="container">
          <?php if ($is_event) : ?>
            <div class="event-details">
              <h2>Event Details</h2>

              <?php
              // Event Date
              $event_date = get_post_meta($post_id, 'WooCommerceEventsDate', true);
              if (!empty($event_date)) {
                echo '<p><strong>Date: </strong>' . esc_html($event_date) . '</p>';
              }

              // Event Start Time
              $event_hour = get_post_meta($post_id, 'WooCommerceEventsHour', true);
              $event_minutes = get_post_meta($post_id, 'WooCommerceEventsMinutes', true);
              $event_timezone = get_post_meta($post_id, 'WooCommerceEventsTimeZone', true);
              if (!empty($event_hour)) {
                echo '<p><strong>Start time: </strong>' . esc_html($event_hour . ':' . $event_minutes) . ' ';
                if (!empty($event_timezone)) {
                  // Extract the timezone abbreviation (CEST, CET, etc.)
                  $timezone_parts = explode('/', $event_timezone);
                  $timezone_abbr = date_create('now', timezone_open($event_timezone))->format('T');
                  echo '<span class="fooevents-tab-timezone" title="' . esc_attr($event_timezone) . '">' . esc_html($timezone_abbr) . '</span>';
                }
                echo '</p>';
              }

              // Event End Time
              $event_hour_end = get_post_meta($post_id, 'WooCommerceEventsHourEnd', true);
              $event_minutes_end = get_post_meta($post_id, 'WooCommerceEventsMinutesEnd', true);
              if (!empty($event_hour_end)) {
                echo '<p><strong>End time: </strong>' . esc_html($event_hour_end . ':' . $event_minutes_end) . ' ';
                if (!empty($event_timezone)) {
                  $timezone_abbr = date_create('now', timezone_open($event_timezone))->format('T');
                  echo '<span class="fooevents-tab-timezone" title="' . esc_attr($event_timezone) . '">' . esc_html($timezone_abbr) . '</span>';
                }
                echo '</p>';
              }

              // Event Venue
              $event_venue = get_post_meta($post_id, 'WooCommerceEventsLocation', true);
              if (!empty($event_venue)) {
                echo '<p><strong>Venue: </strong>' . esc_html($event_venue) . '</p>';
              }

              // Event Phone
              $event_phone = get_post_meta($post_id, 'WooCommerceEventsSupportContact', true);
              if (!empty($event_phone)) {
                echo '<p><strong>Phone: </strong>' . esc_html($event_phone) . '</p>';
              }

              // Event Email
              $event_email = get_post_meta($post_id, 'WooCommerceEventsEmail', true);
              if (!empty($event_email)) {
                echo '<p><strong>Email: </strong>' . esc_html($event_email) . '</p>';
              }

              // Check for custom product attributes
              $product_attributes = get_post_meta($post_id, '_product_attributes', true);
              if (is_array($product_attributes)) {
                // Location (if not already shown)
                if (isset($product_attributes['location']) && !empty($product_attributes['location']['value']) && empty($event_venue)) {
                  echo '<p><strong>Location: </strong>' . esc_html($product_attributes['location']['value']) . '</p>';
                }

                // Days (if available)
                if (isset($product_attributes['days']) && !empty($product_attributes['days']['value'])) {
                  echo '<p><strong>Days: </strong>' . esc_html($product_attributes['days']['value']) . '</p>';
                }

                // Any other attributes you want to display
                // For example:
                if (isset($product_attributes['type']) && !empty($product_attributes['type']['value'])) {
                  echo '<p><strong>Type: </strong>' . esc_html($product_attributes['type']['value']) . '</p>';
                }
              }
              ?>
            </div>
          <?php endif; ?>
        </div>
      </article>
    </main>

<?php endwhile;
endif; ?>

<?php get_footer(); ?>