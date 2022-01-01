<?php
  // Ensure user logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
  }
  else {
    require_once('database.php');
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('HTTP/1.0 403 Forbidden');
      exit;
    }
    else {
      $response = getPing($user);
      print json_encode($response);
      exit;
    }
  }
