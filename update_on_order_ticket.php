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
    $result = db_update_on_order_ticket($post["ticket_id"], $user["user_name"], $post["notes"]);
    if ($result) {
        common_echo_success("Ticket updated/on order in the database!");
    } else {
        throw new Exception("ERROR: update_on_order_ticket.php; Ticket update failed! (CAUSE UNKNOWN)");
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
