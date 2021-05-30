<?php
require_once('common.php');

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

function db_create_log($log)
{
    // Establish database connection
    $conn = db_connect();
    // Prepare the query
    $query = $conn -> prepare(
        "INSERT INTO `dynastyod`.`list_logs`
        (`list_log_id`, `user_id`, `log_data`, `timestamp`)
        VALUES (NULL, ?, ?, ?);"
    );
    // Attach the username argument provided
    $query -> bind_param("sss", $log['user_id'], $log['log_data'], $log['timestamp']);
    // Execute and store the result of the query
    $success = $query->execute();
    // Return the status of the query (success, true or false)
    return $success;
}

/**
 * Creates a new ticket in the database using INSERT.
 * @param array $td Ticket data; should contain all keys for initial ticket creation
 * @return boolean true if the INSERT query succeeds
 */
function db_create_ticket($td)
{
    // Establish database connection
    $conn = db_connect();
    // Prepare the query
    $query = $conn -> prepare(
        "INSERT INTO `dynastyod`.`tickets`
        (`ticket_id`, `first_name`, `last_name`,
        `address1`, `city`, `state`, `zip`,
        `phone`, `email`,
        `summary`, `report_date`, `comp_date`, `type`,
        `notes`, `user_name`, `accept_date`, `billing`, `sched_date`)
        VALUES
        (NULL, ?, ?,
        ?, ?, ?, ?,
        ?, ?,
        ?, ?, '', ?,
        '', '', '', '', '');"
    );
    // Attach the username argument provided
    $query -> bind_param(
        "sssssssssss",
        $td['first_name'],
        $td['last_name'],
        $td['address1'],
        $td['city'],
        $td['state'],
        $td['zip'],
        $td['phone'],
        $td['email'],
        $td['summary'],
        $td['report_date'],
        $td['type']
    );
    // Execute and store the result of the query
    $success = $query->execute();
    // Return the status of the query (success, true or false)
    return $success;
}

/**
 * Gets all tickets from the database
 * @return array contains all tickets as objects
 */
function db_get_all_tickets()
{
    // Establish database connection
    $conn = db_connect();
    // Run SQL query to get all tickets
    $result = $conn->query("SELECT * FROM tickets;");
    // Return the result processed into an array
    return db_result_array($result);
}

/**
 * Gets all the tickets not yet accepted from the database
 * @return array contains all tickets as objects
 */
function db_get_unclaimed_tickets()
{
    // Establish database connection
    $conn = db_connect();
    // Prepare the query, with the user field being unknown
    $query = $conn -> prepare("SELECT * FROM tickets WHERE accept_date='0000-00-00';");
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

    // Query to find all lists belonging to the user
    $query = $conn -> prepare("SELECT list_id FROM user_lists WHERE user_id=?;");
    $query -> bind_param("s", $user_id);
    $query->execute();
    $query_user_lists = db_result_array($query->get_result());

    // Handle each of the lists which belong to the user
    foreach ($query_user_lists as $row_user_list_info) {
        // Create array of items' names
        // Query to get all the entries inside this checklist
        $query = $conn -> prepare("SELECT list_item_id FROM lists WHERE list_id=?;");
        $query -> bind_param("s", $row_user_list_info["list_id"]);
        $query->execute();
        $query_lists = db_result_array($query->get_result());

        // Process each entry in the given user list
        foreach ($query_lists as $row_list_entry) {
            // Query to get the actual list item
            $query = $conn -> prepare("SELECT list_item_name FROM list_items WHERE list_item_id=?;");
            $query -> bind_param("s", $row_list_entry["list_item_id"]);
            $query->execute();
            $query_list_items = db_result_array($query->get_result());

            // Process each list item
            foreach ($query_list_items as $row_item) {
                $arr_item_names[] = $row_item["list_item_name"];
            }
        }

        // Array of items' names has been created, now we need this checklists name
        // Query to get name of the given checklist
        $query = $conn -> prepare("SELECT list_name FROM list_name WHERE list_id=? LIMIT 1;");
        $query -> bind_param("s", $row_user_list_info["list_id"]);
        $query->execute();
        $query_list_name = db_result_array($query->get_result());

        // Associative array representing a single list belonging to the user
        $map_user_list = [];
        $map_user_list["list_name"] = $query_list_name[0]["list_name"];
        $map_user_list["list_items"] = $arr_item_names;
        $result[] = $map_user_list;
    }

    // Return the result processed into an array
    return $result;
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

function db_get_ticket_by_id($ticket_id)
{
    // Establish database connection
    $conn = db_connect();
    // Prepare the query, with the ticket_id field being unknown
    $query = $conn -> prepare("SELECT * FROM tickets WHERE ticket_id=?;");
    // Attach the ticket_id argument provided
    $query -> bind_param("s", $ticket_id);
    // Execute and store the result of the query
    $query->execute();
    $result = $query->get_result();
    // Return the result processed into an array
    return db_result_array($result);
}

function db_update_accept_ticket($ticket_id, $user_name)
{
    // Establish database connection
    $conn = db_connect();
    // Prepare the query
    $query = $conn -> prepare("UPDATE tickets SET user_name=?, accept_date=CURDATE() WHERE ticket_id=? AND user_name='';");
    // Attach the username argument provided
    $query -> bind_param("ss", $user_name, $ticket_id);
    // Execute and store the result of the query
    $success = $query->execute();
    // Return the status of the query (success, true or false)
    return $success;
}

function db_update_schedule_ticket($ticket_id, $user_name, $sched_date, $summary)
{
    // Establish database connection
    $conn = db_connect();
    // Prepare the query
    $query = $conn -> prepare("UPDATE tickets SET sched_date=?, summary=? WHERE ticket_id=? AND user_name=?;");
    // Attach the username argument provided
    $query -> bind_param("ssss", $sched_date, $summary, $ticket_id, $user_name);
    // Execute and store the result of the query
    $success = $query->execute();
    // Return the status of the query (success, true or false)
    return $success;
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
