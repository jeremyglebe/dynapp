<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get an authenticated user
    $user = common_user();
    $tickets = db_get_on_order_tickets();
    echo ncode_json($tickets);
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
