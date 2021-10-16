<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Try to get an authenticated user
    $user = common_user();
    // Only if an authenticated user exists, has a usertype, and is admin
    // -> we can insert a new ticket
    if (isset($user["usertype_cd"]) && $user["usertype_cd"] == 'A') {
        $result = db_create_ticket($post);
        if ($result) {
            common_echo_success("Ticket created in the database!");
        }else{
            throw new Exception("ERROR: create_ticket.php; Ticket creation failed! (CAUSE UNKNOWN)");
        }
    } else {
        throw new Exception('ERROR: create_ticket.php; Requesting user lacks permission! (Must be admin)');
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
