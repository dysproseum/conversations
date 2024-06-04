<?php
  // Ensure logged in user.
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
  $_SESSION['message'] = "Deleting post... ";

  // @todo check access.
  $_SESSION['message'] .= "Not ready yet.";
  header('Location: /conversations/dashboard.php');
  exit;

  // @todo remove access.

  // Delete post action.
  if (isset($_POST['action'])) {
    if ($_POST['action'] == 'delete') {
      if (deletePost($_POST['id'])) {
        $_SESSION['message'] .= "Post deleted.";
        header('Location: /conversations/dashboard.php');
        exit;
      }
      else {
        $_SESSION['message'] .= "Failed to delete post.";
        header('Location: /conversations/delete.php?id=' . $_POST['id']);
        exit;
      }
    }
  }

  header('Location: /conversations/dashboard.php');
  exit;
