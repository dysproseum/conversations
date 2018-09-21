<?php
  // Ensure logged in user.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    global $link;
    require_once('database.php');
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('Location: /conversations/login.php');
      exit;
    }
  }

  // Create new post in database.
  // @todo sanitize this insert query
  $post = $_POST;
  $query = "INSERT INTO posts (uid, created, link, body) VALUES (
  '" . $user->id . "',
  '" . time() . "',
  '" . $post['link'] . "',
  '" . $post['body'] . "'
  )";
  $result = mysqli_query($link, $query);
  if (!$result) {
    print $query;
    print "database error";
    exit;
  }
  $id = mysqli_insert_id($link);

  // Set user id for recipient.
  // @todo sanitize this query
  $recipient = $post['recipient'];
  $query = "SELECT id FROM users WHERE email like '" . $recipient . "' LIMIT 1";
  $result = mysqli_query($link, $query);
  if (!$result) {
    print $query;
    print "database error";
    exit;
  }
  foreach ($result as $row) {
    $uid = $row['id'];
  }

  // Set up access for OP and recipient.
  $query = "INSERT INTO access (id, uid) VALUES (
  '" . $id . "',
  '" . $user->id . "'
  ), (
  '" . $id . "',
  '" . $uid . "'
  )";
  $result = mysqli_query($link, $query);
  if (!$result) {
    print $query;
    print "database error";
    exit;
  }

  // @todo Notifications
  // Email user an invite if they don't exist
  // Browser notification for user that does exist

  // Redirect to post.
  header('Location: /conversations/post.php?id=' . $id);

