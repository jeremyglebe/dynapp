<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get an authenticated user
    $user = common_user();
    // Only if an authenticated user exists, has a usertype, and is admin
    // -> we can provide them with all the tickets
    if (isset($user["usertype_cd"]) && $user["usertype_cd"] == 'A') {
        $tickets = db_get_all_tickets();
        echo ncode_json($tickets);
    } else {
        throw new Exception('ERROR: get_all_tickets.php; Requesting user lacks permission! (Must be admin)');
    }
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
