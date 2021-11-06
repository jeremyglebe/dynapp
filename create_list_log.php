<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Try to get an authenticated user
    $user = common_user();
    // If user is logged in, submit the log
    $result = db_create_log($post);
    if ($result) {
        common_echo_success("Log submitted to the database!");
    } else {
        throw new Exception("ERROR: create_list_log.php; Ticket creation failed! (CAUSE UNKNOWN)");
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
