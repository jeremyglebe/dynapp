<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Get data from post request
    $post = common_post();
    // Make sure that POST data includes a ticket_id
    if (!isset($post["ticket_id"])) {
        throw new Exception('ERROR: get_ticket_by_id.php; POST request must include property "ticket_id"!');
    }
    // Try to get an authenticated user
    $user = common_user();
    // Get the specified ticket, but don't provide it to the user yet
    $tickets = db_get_ticket_by_id($post["ticket_id"]);
    // User is admin or ticket belongs to user or ticket is unclaimed
    if ($user["usertype_cd"] == 'A' || $tickets[0]["user_name"] == $user["user_name"] || $tickets[0]["user_name"] == '') {
        // Provide a json of the first (and hopefully only) ticket with that id
        echo ncode_json($tickets[0]);
    } else {
        throw new Exception('ERROR: get_ticket_by_id.php; Requesting user lacks permission! (Must be admin or requesting their own ticket or requesting an unaccepted ticket)');
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
