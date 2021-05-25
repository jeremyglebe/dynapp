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
    if ($user && ($user["user_id"] == $post["user_id"] || $user["usertype_cd"] == 'A')) {
        // Get the user's checklists
        $checklists = db_get_user_checklists($post["user_id"]);
        // Provide a json of the checklists
        echo ncode_json($checklists);
    } else {
        echo "null";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
