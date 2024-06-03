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
      $response = getPing($id, $cid);
      $comment = $response['comment'];
      if ($comment['id'] == $cid) {
        header('HTTP/1.1 304 Not Modified');
        exit;
      }
      else {
        require_once('include/template.php');
        $last = getComment($cid);
        $current_img = $last['picture'];
        $current_day = date('d', $last['created']);
        $response['html'] = buildComment($comment, $current_img, $current_day);
      }
      print json_encode($response, JSON_PRETTY_PRINT);
      exit;
    }
  }
