<?php
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

  $post = $_POST;

  // @todo sanitize this insert query
  $parent_id = $post['parent_id'];
  $query = "INSERT INTO posts (parent_id, uid, created, link, body) VALUES (
  '" . $parent_id . "',
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
  else {
    header('Location: /conversations/post.php?id=' . $parent_id);
  }

