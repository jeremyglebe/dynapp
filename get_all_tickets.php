<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get an authenticated user
    $user = common_user();
    // Only if an authenticated user exists, has a usertype, and is admin
    // -> we can provide them with all the tickets
    if ($user && isset($user["usertype_cd"]) && $user["usertype_cd"] == 'A') {
        $tickets = db_get_all_tickets();
        echo ncode_json($tickets);
    } else {
        // Any other circumstances result in null
        echo "null";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
