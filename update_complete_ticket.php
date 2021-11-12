<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Try to get an authenticated user
    $user = common_user();
    // Update the ticket to belong to the current user
    $result = db_update_complete_ticket($post["ticket_id"], $user["user_name"], $post["comp_date"], $post["billing"], $post["summary"]);
    // Test for the result
    if ($result) {
        common_echo_success("Ticket updated/completed in the database!", $post["ticket_id"], $user["user_name"], $post["comp_date"], $post["billing"], $post["summary"]);
    } else {
        throw new Exception("ERROR: update_complete_ticket.php; Ticket update failed! (CAUSE UNKNOWN)");
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
