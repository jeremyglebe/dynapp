<?php
// Some code was borrowed or modified from the following.
// Credit: Time to Hack author "Pankaj"
// https://time2hack.com/upload-files-to-php-backend-using-fetch-formdata/
require_once("services/common.php");
require_once("services/ncode.php");

// 500 MiB
const BYTE_LIMIT = 500*1024*1024;

try {
    // Get the ticket number associated with this image upload
    $post = common_post();
    if (!isset($post['ticket_id'])) {
        throw new Exception('ERROR: upload_ticket_image.php; Ticket id not specified!');
    }
    $ticket_id = $post['ticket_id'];
    // Authenticate user using request's PIN before doing anything else
    $user = common_user();

    // The date
    $stamp = time();

    // We're processing multiple images, so we will need to wrap the output in an array
    // for returning JSON. Just echoing the outer brackets.
    echo "[";

    // We will need to test and move each uploaded file
    for ($i = 0; $i < count($_FILES); $i++) {
        // Uploaded files must have an "error" property, on success this property is an "ok" flag
        // Checking this property ensures data isn't undefined and also prevents $_FILES corruption attacks
        if (!isset($_FILES["ticket_image-$i"]['error'])) {
            throw new Exception('ERROR: upload_ticket_image.php; Invalid parameters on uploaded files!');
        }

        // Check for any errors on uploaded files
        $ferr = $_FILES["ticket_image-$i"]['error'];
        if ($ferr === UPLOAD_ERR_NO_FILE) {
            throw new Exception('ERROR: upload_ticket_image.php; No file sent!');
        } elseif ($ferr === UPLOAD_ERR_INI_SIZE || $ferr === UPLOAD_ERR_FORM_SIZE) {
            throw new Exception('ERROR: upload_ticket_image.php; Uploaded files exceed size limit! (UPLOAD_ERR)');
        } elseif ($ferr !== UPLOAD_ERR_OK) {
            throw new Exception('ERROR: upload_ticket_image.php; Unknown error found in uploaded files!');
        }

        // Check size once more, against a hardcoded number (in bytes)
        if ($_FILES["ticket_image-$i"]['size'] > BYTE_LIMIT) {
            throw new Exception('ERROR: upload_ticket_image.php; Uploaded files exceed size limit! (BYTE_LIMIT)');
        }

        // Check MIME type ($_FILES["ticket_image-$i"]['mime'] is untrustworthy)
        // Honestly, I don't entirely understand this part. This is one of the things I found online
        // as a security tip.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $ext = array_search(
            $finfo->file($_FILES["ticket_image-$i"]['tmp_name']), // Searching for
            array('jpg' => 'image/jpeg','png' => 'image/png','gif' => 'image/gif'), // Searching in
            true // Strict search
        );
        if (false === $ext) {
            throw new Exception('ERROR: upload_ticket_image.php; Invalid file format!');
        }

        // Copy the file to a new location
        $move_result = move_uploaded_file(
            $_FILES["ticket_image-$i"]['tmp_name'], // source file
            "./uploads/ticket-$ticket_id-$stamp-$i.$ext" // destination to save file
        );
        if (!$move_result) {
            throw new Exception('ERROR: upload_ticket_image.php; Failed to store uploaded file!');
        }

        // Upload the image to ticket image lists
        $result = db_create_image_listing(array("ticket_id"=>$ticket_id, "image"=>"ticket-$ticket_id-$stamp-$i.$ext"));
        if ($result) {
            common_echo_success("Image listed in the database!");
            // Add a comma for returning json list to requester
            echo ",";
        } else {
            throw new Exception("ERROR: upload_ticket_image.php; Image listing failed! (CAUSE UNKNOWN)");
        }
    }
    
    // If no errors occurred, just send a feedback object to indicate success
    common_echo_success('Files uploaded successfully');
    
    // Finishing the JSON array text
    echo "]";
}
// Catch any errors and echo them back
catch (Exception $e) {
    common_echo_error($e);
    // Finishing the JSON array text
    echo "]";
}
