<?php
// --- ENTERCASHLESS API Integration for tickets ---

add_action('save_post_event_magic_tickets', 'add_ticket_to_entercashless', 10, 3);

function add_ticket_to_entercashless($post_id, $post, $update) {
    // Skip auto-drafts
    if ($post->post_status === 'auto-draft') return;

    // For new posts, delay processing
    if (!$update) {
        wp_schedule_single_event(time() + 5, 'delayed_entercashless_processing', array($post_id));
        return;
    }

    // For updates, process immediately
    process_entercashless_ticket($post_id);
}

// Register delayed hook
add_action('delayed_entercashless_processing', 'process_entercashless_ticket');

function process_entercashless_ticket($post_id) {
    // Avoid duplicates
    $processed = get_post_meta($post_id, '_entercashless_processed', true);
    if ($processed) return;

    error_log("=== Processing EnterCashless ticket ID: $post_id ===");
    $all_meta = get_post_meta($post_id);
    error_log('All ticket meta: ' . print_r($all_meta, true));

    // Get event/product ID
    $event_id = get_post_meta($post_id, 'WooCommerceEventsProductID', true);
    if (!$event_id) {
        error_log("No event ID found for ticket $post_id");
        return;
    }

    // Ticket ID
    $ticket_id = get_post_meta($post_id, 'WooCommerceEventsTicketID', true);
    if (!$ticket_id) {
        error_log("No ticket ID found for ticket $post_id");
        return;
    }

    // Holder info
    $first_name = get_post_meta($post_id, 'WooCommerceEventsPurchaserFirstName', true);
    $last_name  = get_post_meta($post_id, 'WooCommerceEventsPurchaserLastName', true);
    $email      = get_post_meta($post_id, 'WooCommerceEventsPurchaserEmail', true); // adjust meta key if different
    $holder_name = trim("$first_name $last_name");

    if (!$holder_name || !$email) {
        $holder_name = $holder_name ?: 'Unknown';
        $email = $email ?: 'unknown@example.com';
    }

    $api_key = 'YOUR_ENTERCASHLESS_API_KEY';
    $url = "https://app.entercashless.com/api/events/$event_id/issue-ticket";

    $body = array(
        'holder_name'      => $holder_name,
        'holder_email'     => $email,
        'import_reference' => 'api',
        'ticket_id'        => (int)$ticket_id,
    );

    $response = wp_remote_post($url, array(
        'body'       => json_encode($body),
        'headers'    => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ),
        'timeout'    => 30,
    ));

    // Log the response
    if (is_wp_error($response)) {
        error_log('EnterCashless API error: ' . $response->get_error_message());
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        error_log("EnterCashless API response code: $response_code");
        error_log("EnterCashless API response body: $response_body");

        // Mark as processed if successful
        if ($response_code >= 200 && $response_code < 300) {
            update_post_meta($post_id, '_entercashless_processed', true);
        }
    }
}
