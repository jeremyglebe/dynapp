<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Make sure that POST data includes a user_name
    if (!isset($post["ticket_id"])) {
        throw new Exception('ERROR: get_ticket_images.php; POST request must include property "ticket_id"!');
    }
    // Try to get an authenticated user
    $user = common_user();
    // Get the user's tickets
    $images = db_get_image_list($post["ticket_id"]);
    // Provide a json of the tickets
    echo ncode_json($images);
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
