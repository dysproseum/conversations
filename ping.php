<?php
  // Ensure user logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
  }
  else {
    require_once('include/database.php');
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('HTTP/1.0 403 Forbidden');
      exit;
    }
    else {
      $id = (int) $_REQUEST['id'];
      $cid = (int) $_REQUEST['comment'];
      print getPing($id, $cid);
      exit;
    }
  }
