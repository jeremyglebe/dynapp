<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get an authenticated user
    $user = common_user();
    // Output the authenticated user
    echo ncode_json($user);
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
