<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Make sure that POST data includes a user_name
    if(!isset($post["user_name"])){
        throw new Exception('ERROR: get_user_tickets.php; POST request must include property "user_name"!');
    }
    // Try to get an authenticated user
    $user = common_user();
    // If the user matches the provided username
    if ($user["user_name"] == $post["user_name"] || $user["usertype_cd"] == 'A') {
        // Get the user's tickets
        $tickets = db_get_user_tickets($post["user_name"]);
        // Provide a json of the tickets
        echo ncode_json($tickets);
    } else {
        throw new Exception('ERROR: get_user_tickets.php; Requesting user lacks permission! (Must be admin or requesting their own lists)');
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
