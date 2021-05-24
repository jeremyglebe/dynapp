<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Try to get an authenticated user
    $user = common_user();
    // If the user matches the provided username
    if ($user && ($user["user_name"] == $post["user_name"] || $user["usertype_cd"] == 'A')) {
        // Get the user's tickets
        $tickets = db_get_user_tickets($post["user_name"]);
        // Provide a json of the tickets
        echo ncode_json($tickets);
    } else {
        echo "null";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
