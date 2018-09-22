<?php
  // Ensure user logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('database.php');
    global $mysqli;
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('Location: /conversations/login.php');
      exit;
    }
  }

  $post = $_POST;

  // Insert new comment in the database.
  $parent_id = $post['parent_id'];
  $stmt = $mysqli->prepare("INSERT INTO posts (parent_id, uid, created, link, body) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param('iiiss',
    $parent_id,
    $user->id,
    time(),
    $post['link'],
    $post['body']
  );
  $stmt->execute();
  $stmt->close();

  // @todo Notifications
  // Browser notification for user that does exist

  // Redirect to post page.
  // @todo convert to ajax
  header('Location: /conversations/post.php?id=' . $parent_id);
  exit;

