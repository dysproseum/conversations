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
  else {
    $id = mysqli_insert_id($link);
    header('Location: /conversations/post.php?id=' . $id);
  }

