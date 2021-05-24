<?php

/**
 * Establishes connection to the mysql database server-side
 * @return \mysqli a result object from the mysql connection
 */
function db_connect()
{
    // Create the connection to the mysql database and store result
    $result = new mysqli('127.0.0.1', 'root', 't4Gb#KzTq', 'dynastyod');
    if (!$result) {
        // Error!
        throw new Exception('ERROR: db_connect(); Connection failed!');
    } else {
        // Return the query result (mostly null properties)
        return $result;
    }
}
/**
 * Gets the results of an SQL query as an array of objects
 * @param \mysqli_result $result the result of an already executed sql query
 * @return array contains all result rows as objects
 */
function db_result_array(\mysqli_result $result)
{
    if (!$result) {
        // Error!
        throw new Exception('ERROR: db_result_array(); No query result!');
    } else {
        $rows = [];
        // Stupid PHP syntax that is basically "for row in result" (also, associative means key-value)
        while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
            // Extra stupid PHP syntax to declare an array... This appends each iteration
            $rows[] = $row;
        }
        // Return the array containing the row objects
        return $rows;
    }
}

/**
 * Gets all tickets from the database
 * @return array contains all tickets as objects
 */
function db_get_all_tickets()
{
    try {
        // Establish database connection
        $conn = db_connect();
        // Run SQL query to get all tickets
        $result = $conn->query("SELECT * FROM tickets;");
        // Return the result processed into an array
        return db_result_array($result);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

/**
 * Gets all the tickets of a specific user from the database
 * @return array contains all tickets as objects
 */
function db_get_user_tickets($username)
{
    // Establish database connection
    $conn = db_connect();
    // Prepare the query, with the user field being unknown
    $query = $conn -> prepare("SELECT * FROM tickets WHERE user_name=?;");
    // Attach the username argument provided
    $query -> bind_param("s", $username);
    // Execute and store the result of the query
    $query->execute();
    $result = $query->get_result();
    // Return the result processed into an array
    return db_result_array($result);
}

function db_get_user_checklists($user_id)
{
    $result = [];

    // Establish database connection
    $conn = db_connect();

    // Query to find out what lists belong to the user
    $query = $conn -> prepare("SELECT * FROM user_lists WHERE user_id=?;");
    $query -> bind_param("s", $user_id);
    $query->execute();
    $user_lists = db_result_array($query->get_result());

    // Process each user list
    foreach ($user_lists as $ulist) {
        // Query to get the items inside this checklist
        $query = $conn -> prepare("SELECT * FROM lists WHERE list_id=?;");
        $query -> bind_param("s", $ulist["list_id"]);
        $query->execute();
        $list_item_ids = db_result_array($query->get_result());

        // Process each list item id
        foreach ($list_item_ids as $id) {
            // Query to get the actual list item
            $query = $conn -> prepare("SELECT * FROM list_items WHERE list_item_id=?;");
            $query -> bind_param("s", $id["list_item_id"]);
            $query->execute();
            $list_items = db_result_array($query->get_result());

            // Process each list item
            foreach ($list_items as $item) {
                $result[] = $item["list_item_name"];
            }
        }
    }

    // Return the result processed into an array
    return $result;
}
