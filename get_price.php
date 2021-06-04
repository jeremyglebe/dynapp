<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get post data
    $post = common_post();
    // Make sure that POST data includes a product name
    if(!isset($post["product"])){
        throw new Exception('ERROR: get_user_tickets.php; POST request must include property "product"!');
    }
    // Try to get an authenticated user
    $user = common_user();
    // Output the authenticated user
    echo ncode_json(db_get_price($post["product"]));
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
