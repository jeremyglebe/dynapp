<?php
require_once("db.php");

/**
 * Attempts to login with the given PIN, providing the associated user
 * @param string $pin is a six digit code the user submits to login
 * @return array|null either the user's data or, if login failed, null
 */
function auth_pin($pin)
{
    try {
        // Create a connection to the database
        $conn = db_connect();
        // Prepare SQL query to verify username and password
        $query = $conn -> prepare("SELECT * FROM `users` WHERE `password`=?;");
        // Bind username and password to the parameters in the query
        $query->bind_param("s", $pin);
        // Execute and store the result of the query
        $query->execute();
        $result = $query->get_result();
        // Test if the username and password was found
        if ($result->num_rows == 1) {
            // Extract the first row from the results
            $user_row = db_result_array($result)[0];
            // Remove some properties that shouldn't or needn't be sent elsewhere
            unset($user_row["password"]);
            unset($user_row["last_login_ts"]);
            // Return true to show that we logged in
            return $user_row;
        }
        // If we haven't successfully logged in, return null
        return null;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
