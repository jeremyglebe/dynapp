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
    $result = db_update_ticket_notes($post["ticket_id"], $post["notes"]);
    if ($result) {
        common_echo_success("Ticket updated in the database!");
    } else {
        throw new Exception("ERROR: update_ticket_notes.php; Ticket update failed! (CAUSE UNKNOWN)");
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
