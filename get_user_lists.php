<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Make sure that POST data includes a user_id
    if (!isset($post["user_id"])) {
        throw new Exception('ERROR: get_user_lists.php; POST request must include property "user_id"!');
    }
    // Try to get an authenticated user
    $user = common_user();
    // If the user matches the provided username
    if ($user["user_id"] == $post["user_id"] || $user["usertype_cd"] == 'A') {
        // Get the user's checklists
        $checklists = db_get_user_checklists($post["user_id"]);
        // Provide a json of the checklists
        echo ncode_json($checklists);
    } else {
        throw new Exception('ERROR: get_user_lists.php; Requesting user lacks permission! (Must be admin or requesting their own lists)');
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
