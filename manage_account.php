<?php

// Ensure user logged in.
session_start();
require_once('include/database.php');
$user = getUserInfo($_SESSION['sub']);
if (!$user) {
  header('HTTP/1.0 403 Forbidden');
  exit;
}

if (!isset($_POST['notify'])) {
  header('HTTP/1.1 500 Server Error');
  print "No param specified";
  exit;
}
$notify = $_POST['notify'];
updateNotify($user->sub, $notify);

print "OK";
exit;
