<?php
require_once('services/common.php');
require_once('services/db.php');
require_once('services/ncode.php');

try {
    // Try to get an authenticated user
    $user = common_user();
    // If the user matches the provided username
    // Get the user's checklists
    $checklists = db_get_user_checklists(-1);
    // Provide a json of the checklists
    echo ncode_json($checklists);
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
}
