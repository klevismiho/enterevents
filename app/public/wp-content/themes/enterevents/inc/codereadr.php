<?php
// Create CodeReadr database when product is created and store the database ID
add_action('save_post_product', 'create_codereadr_database_on_publish', 10, 3);

function create_codereadr_database_on_publish($post_id, $post, $update)
{
    // Skip auto-drafts or revisions
    if ($post->post_status === 'auto-draft' || wp_is_post_revision($post_id)) {
        return;
    }

    // Check if we've already created a database for this product
    $database_id = get_post_meta($post_id, '_codereadr_database_id', true);
    if ($database_id) {
        return;
    }

    $api_key = 'a89211804ce0816119682717c6118008';
    $database_name = $post->post_title . ' - ' . $post_id;

    $url = 'https://api.codereadr.com/api/';

    $body = array(
        'section'       => 'databases',
        'action'        => 'create',
        'api_key'       => $api_key,
        'database_name' => $database_name,
    );

    $response = wp_remote_post($url, array(
        'body'    => $body,
        'timeout' => 30,
    ));

    // Store the database ID if creation was successful
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
        $response_body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($response_body);

        if ($xml && $xml->status == 1) {
            $database_id = (string)$xml->id;
            update_post_meta($post_id, '_codereadr_database_id', $database_id);
            error_log('CodeReadr database created with ID: ' . $database_id);

            // Verify the meta was saved
            $saved_id = get_post_meta($post_id, '_codereadr_database_id', true);
            error_log('Saved database ID in post meta: ' . $saved_id);
            error_log('Post ID where meta was saved: ' . $post_id);
        }
    }
}



// Update CodeReadr database name when product is updated
add_action('save_post_product', 'update_codereadr_database_on_update', 20, 3);

function update_codereadr_database_on_update($post_id, $post, $update)
{
    // Skip auto-drafts and revisions
    if ($post->post_status === 'auto-draft' || wp_is_post_revision($post_id)) {
        return;
    }

    // Only run on updates, not new posts
    if (!$update) {
        return;
    }

    // Get the existing database ID
    $database_id = get_post_meta($post_id, '_codereadr_database_id', true);
    if (!$database_id) {
        error_log('No CodeReadr database ID found for product update: ' . $post_id);
        return; // No database exists for this product
    }

    // Create new database name with updated title
    $new_database_name = $post->post_title . ' - ' . $post_id;
    
    error_log('Updating CodeReadr database ID: ' . $database_id . ' with new name: ' . $new_database_name);

    $api_key = 'a89211804ce0816119682717c6118008';
    $url = 'https://api.codereadr.com/api/';

    $body = array(
        'section'       => 'databases',
        'action'        => 'update',
        'api_key'       => $api_key,
        'database_id'   => $database_id,
        'database_name' => $new_database_name,
    );

    $response = wp_remote_post($url, array(
        'body'    => $body,
        'timeout' => 30,
    ));

    // Log the response for debugging
    if (is_wp_error($response)) {
        error_log('CodeReadr database update API error: ' . $response->get_error_message());
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        error_log('CodeReadr database update response code: ' . $response_code);
        error_log('CodeReadr database update response body: ' . $response_body);
    }
}



// Simple solution with delayed processing for new tickets
add_action('save_post_event_magic_tickets', 'add_ticket_to_codereadr_database', 10, 3);

function add_ticket_to_codereadr_database($post_id, $post, $update)
{
    // Skip auto-drafts
    if ($post->post_status === 'auto-draft') {
        return;
    }

    // For new posts (not updates), delay processing to ensure meta data is saved
    if (!$update) {
        // Use wp_schedule_single_event for a 5-second delay
        wp_schedule_single_event(time() + 5, 'delayed_codereadr_processing', array($post_id));
        return;
    }

    // For updates, process immediately
    process_codereadr_ticket($post_id);
}

// Register the delayed processing hook
add_action('delayed_codereadr_processing', 'process_codereadr_ticket');

function process_codereadr_ticket($post_id)
{
    // Prevent duplicate processing
    $processed = get_post_meta($post_id, '_codereadr_processed', true);
    if ($processed) {
        return;
    }

    // DEBUG: Show all meta fields for this specific ticket
    error_log('=== Processing ticket ID: ' . $post_id . ' ===');
    $all_meta = get_post_meta($post_id);
    error_log('All ticket meta fields: ' . print_r($all_meta, true));

    // Get the event/product ID for this ticket
    $event_id = get_post_meta($post_id, 'WooCommerceEventsProductID', true);
    error_log('Event ID from meta: "' . $event_id . '"');
    
    if (!$event_id) {
        error_log('No event ID found for ticket: ' . $post_id);
        return;
    }

    // Get the CodeReadr database ID for this event
    $database_id = get_post_meta($event_id, '_codereadr_database_id', true);
    if (!$database_id) {
        error_log('No CodeReadr database ID found for event: ' . $event_id);
        return;
    }

    // Get the ticket number
    $ticket_number = get_post_meta($post_id, 'WooCommerceEventsTicketID', true);
    if (!$ticket_number) {
        error_log('No ticket number found for ticket: ' . $post_id);
        return;
    }

    // Get the ticket holder's name
    $first_name = get_post_meta($post_id, 'WooCommerceEventsPurchaserFirstName', true);
    $last_name = get_post_meta($post_id, 'WooCommerceEventsPurchaserLastName', true);
    $full_name = trim($first_name . ' ' . $last_name);
    
    // Get the event name
    $event_name = get_post_meta($post_id, 'WooCommerceEventsProductName', true);
    
    // Construct the response text: "Full Name - Event Name"
    $response_text = '';
    if (!empty($full_name)) {
        $response_text = $full_name;
    }
    if (!empty($event_name)) {
        $response_text .= (!empty($response_text) ? ' - ' : '') . $event_name;
    }
    
    // Fallback if both are empty
    if (empty($response_text)) {
        $response_text = 'Unknown';
    }

    error_log('Adding ticket: ' . $ticket_number . ' to database: ' . $database_id . ' with name: ' . $response_text);

    $api_key = 'a89211804ce0816119682717c6118008';
    $url = 'https://api.codereadr.com/api/';

    $body = array(
        'section'    => 'databases',
        'action'     => 'addvalue',
        'api_key'    => $api_key,
        'database_id' => $database_id,
        'value'      => $ticket_number,
        'response'   => $response_text,
    );

    $response = wp_remote_post($url, array(
        'body'    => $body,
        'timeout' => 30,
    ));

    // Log the response for debugging
    if (is_wp_error($response)) {
        error_log('CodeReadr API error: ' . $response->get_error_message());
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        error_log('CodeReadr API response code: ' . $response_code);
        error_log('CodeReadr API response body: ' . $response_body);
        
        // Mark as processed if successful
        if ($response_code == 200) {
            update_post_meta($post_id, '_codereadr_processed', true);
        }
    }
}