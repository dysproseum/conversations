<?php

// Ensure user logged in.
session_start();
require_once('include/database.php');
$user = getUserInfo($_SESSION['sub']);
if (!$user) {
  header('HTTP/1.0 403 Forbidden');
  exit;
}

if (isset($_POST['notify_banner'])) {
  $banner = $_POST['notify_banner'];
  updateNotify($user->sub, 'notify_banner', $banner);
}

if (isset($_POST['notify_sound'])) {
  $sound = $_POST['notify_sound'];
  updateNotify($user->sub, 'notify_sound', $sound);
}

// If no params, return user prefs so frontend can update.
$out = getNotify($user->sub);
print json_encode($out, JSON_PRETTY_PRINT);
exit;
