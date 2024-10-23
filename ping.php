<?php
  // Ensure user logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
  }

  require_once('include/database.php');
  $user = getUserInfo($_SESSION['sub']);
  if (!$user) {
    header('HTTP/1.0 403 Forbidden');
    exit;
  }

  $id = (int) $_REQUEST['id'];
  $cid = (int) $_REQUEST['comment'];

  // getPing now returns if new comments are available on other posts.
  $response = getPing($id, $cid);

  // Be prepared to be called from non-post pages.
  if ($id == 0 && $cid == 0) {
    print json_encode($response, JSON_PRETTY_PRINT);
    exit;
  }

  // Set status 304 if latest comment has not changed.
  if ($response['latest_id'] == $cid) {
    header('HTTP/1.1 304 Not Modified');
    exit;
  }

  // Render html for comment(s).
  foreach ($response['comments'] as &$comment) {
    require_once('include/template.php');
    $last = getComment($comment['id']);
    $current_img = $last['picture'];
    $current_day = date('d', $last['created']);
    $comment['html'] = buildComment($comment, $current_img, $current_day);
  }

  // Add post titles for notifications.
  foreach ($response['notifications'] as &$comment) {
    $comment['parent_title'] = getTitle($comment['parent_id']);
  }

  print json_encode($response, JSON_PRETTY_PRINT);
  exit;
