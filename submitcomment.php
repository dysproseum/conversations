<?php
  // Ensure user logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('include/database.php');
    global $mysqli;
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('Location: /conversations/login.php');
      exit;
    }
  }
  // Accommodate redirecting back to iframe.
  if (isset($_SERVER['HTTP_REFERER'])) {
    $return_url = $_SERVER['HTTP_REFERER'];
  }
  else {
    $return_url = '/conversations/post.php';
  }

  $post = $_POST;
  $parent_id = $post['parent_id'];

  if (!empty($post['link']) || !empty($post['body'])) {

    $body = strip_tags($post['body']);
    if (!empty($post['link'])) {
      $url = filter_var($post['link'], FILTER_VALIDATE_URL);
      if (!$url) {
        $_SESSION['message'] = 'Invalid URL';
        header('Location: ' . $return_url . '?id=' . $post['parent_id']);
        exit;
      }
    }

    // Insert new comment in the database.
    $stmt = $mysqli->prepare("INSERT INTO posts (parent_id, uid, created, link, body) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiss',
      $parent_id,
      $user->id,
      time(),
      $post['link'],
      $body,
    );
    $stmt->execute();
    $stmt->close();

    // @todo Notifications
    // Browser notification for user that does exist
  }
  else {
    $_SESSION['message'] = 'Empty body and/or link.';
  }

  // Redirect to post page.
  // @todo convert to ajax
  header('Location: ' . $return_url . '?id=' . $parent_id);
  exit;

