<?php

// Ensure Google client key exists.
require_once('config.php');
if (isset($conf['google']) && isset($conf['google']['client_id'])) {
  define('CLIENT_ID', $conf['google']['client_id']);
}
else {
  header('HTTP/1.1 403 Forbidden');
  print "Missing google client id";
  exit;
}
require_once dirname(__FILE__) . '/include/google-api-php-client/vendor/autoload.php';

// Get $id_token via HTTPS POST.
$id_token = $_POST['idtoken'];

$client = new Google_Client(['client_id' => CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
$payload = $client->verifyIdToken($id_token);
if ($payload) {
  $userid = $payload['sub'];
  // If request specified a G Suite domain:
  //$domain = $payload['hd'];
} else {
  // Invalid ID token
  exit('Invalid ID token');
}

// Endpoint to verify token claim.
$url = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $id_token;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Check token and build response.
$info = [];
$result = json_decode($response);
//var_dump($result);
//die;
if ($result->sub !== $userid) {
  header('HTTP/1.1 500 Server Error');
  print "Unable to verify user claim";
  var_dump($user);
  exit;
}

// Set up session variables and
// create a user in the database if necessary.
session_start();
$_SESSION['sub'] = $userid;

require_once('include/database.php');

// Update picture.
$picture = $_POST['picture'];
updatePicture($userid, $picture);

$user = getUserInfo($userid);
if (!$user) {
  $info['message'] = "Creating new user";
  newUser($result);
  $user = getUserInfo($userid);
}
else {
  $info['message'] = "User already exists";
}
$info['user'] = $user;

print json_encode($info, JSON_PRETTY_PRINT);
