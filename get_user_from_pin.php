<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get an authenticated user
    $user = common_user();
    // If an authenticated user exists
    if ($user) {
        echo ncode_json($user);
    } else {
        // If a user doesn't exist with that PIN, echo NULL
        echo "null";
    }
} catch (Exception $e) {
    // Any errors should be echoed back
    echo $e->getMessage();
}
