<?php
require_once('services/auth.php');

// Standard headers for all pages
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Dynamically determine the allowed origin header to include any of a preset list of origins
// Credit: Stack Overflow user "user3638471"
// https://stackoverflow.com/questions/7564832/how-to-bypass-access-control-allow-origin
$http_origin = $_SERVER['HTTP_ORIGIN'];
$allowed_domains = array(
  'capacitor://localhost',
  'http://localhost',
  'http://localhost:8100',
  'http://localhost:5000'
);
if (in_array($http_origin, $allowed_domains)) {
    header("Access-Control-Allow-Origin: $http_origin");
}

function common_post()
{
    // Parse any POST request data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // FormData requests use $_POST
        if ($_POST) {
            return $_POST;
        }
        // JSON requests are going to use php://input
        else {
            return json_decode(file_get_contents('php://input'), true);
        }
    } else {
        throw new Exception('ERROR: common_post(); Request type is not "POST"!');
    }
}

function common_user()
{
    $data = common_post();
    // Check if a PIN is being sent, and provide any USER associated with that PIN
    if ($data && isset($data['pin'])) {
        return auth_pin($data['pin']);
    } else {
        throw new Exception('ERROR: common_user(); No PIN provided!');
    }
}

function common_echo_error(Exception $e)
{
    $response = array(
        "status" => "error",
        "error" => true,
        "message" => $e->getMessage()
    );
    echo ncode_json($response);
}

function common_echo_success($message)
{
    $response = array(
        "status" => "success",
        "error" => false,
        "message" => $message
    );
    echo ncode_json($response);
}
