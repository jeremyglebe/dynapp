<?php
require_once('services/auth.php');

// Set the allowed ORIGIN
// Credit: Stack Overflow user "user3638471"
// https://stackoverflow.com/questions/7564832/how-to-bypass-access-control-allow-origin
$http_origin = $_SERVER['HTTP_ORIGIN'];
$allowed_domains = array(
  'capacitor://localhost',
  'http://localhost',
  'http://localhost:8100'
);
if (in_array($http_origin, $allowed_domains)) {
    header("Access-Control-Allow-Origin: $http_origin");
}

// Allow some headers
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

function common_post()
{
    try {
        // Parse any POST request data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return json_decode(file_get_contents("php://input"), true);
        } else {
            return null;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function common_user()
{
    try {
        $data = common_post();
        // Check if a PIN is being sent, and provide any USER associated with that PIN
        if ($data && isset($data["pin"])) {
            return auth_pin($data["pin"]);
        } else {
            return null;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
