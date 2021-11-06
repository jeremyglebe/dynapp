<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get an authenticated user
    $user = common_user();
    // Get the user's accepted tickets
    $tickets = db_get_user_tickets_accepted($user["user_name"]);
    // Provide a json of the tickets
    echo ncode_json($tickets);
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
