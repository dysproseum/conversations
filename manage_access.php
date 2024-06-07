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

  if (!isset($_POST['action'])) {
    $_SESSION['message'] = 'Invalid action';
    header('Location: /conversations/dashboard.php');
  }
  else {
    $action = $_POST['action'];
  }

  if (!isset($_POST['id'])) {
    $_SESSION['message'] = 'Invalid post id';
    header('Location: /conversations/dashboard.php');
  }
  else {
    $id = $_POST['id'];
  }
  $post = getPost($id);

  if (!checkAccess($post, $user)) {
    header('HTTP/1.1 403 Forbidden');
    exit;
  }

  // Remove access action.
  if ($action == 'remove') {
    if (!isset($_REQUEST['uid'])) {
      $_SESSION['message'] = 'Invalid user id';
      header("Location: /conversations/access.php?id=$id");
      exit;
    }
    else {
      $uid = $_REQUEST['uid'];
    }
    $_SESSION['message'] = 'Removing access... ';

    if (removeAccess($id, $uid)) {
      $_SESSION['message'] .= 'Access removed.';
      if ($user->id == $uid) {
        header('Location: /conversations/dashboard.php');
        exit;
      }
      else {
        header("Location: /conversations/access.php?id=$id");
        exit;
      }
    }
  }

  // Delete post action.
  if ($action == 'delete') {

    // Check access.
    if ($post['uid'] != $user->id) {
      $_SESSION['message'] = 'Only the post author can delete this post.';
      header("Location: /conversations/access.php?id=$id");
      exit;
    }

    $_SESSION['message'] = "Deleting post... ";

    // Remove comments.
    if (deletePostComments($id)) {
      $_SESSION['message'] .= "Comments deleted. ";
    }
    else {
      $_SESSION['message'] .= "Failed to delete comments.";
      header("Location: /conversations/access.php?id=$id");
      exit;
    }

    // Remove access.
    $uids = getPostAccess($id);
    foreach ($uids as $uid) {
      if (removeAccess($id, $uid)) {
        $_SESSION['message'] .= "Access removed. ";
      }
      else {
        $_SESSION['message'] .= "Failed to remove access.";
        header("Location: /conversations/access.php?id=$id");
        exit;
      }
    }

    // Actually delete post.
    if (deletePost($id)) {
      $_SESSION['message'] .= "Post deleted.";
      header('Location: /conversations/dashboard.php');
      exit;
    }
    else {
      $_SESSION['message'] .= "Failed to delete post.";
      header("Location: /conversations/access.php?id=$id");
      exit;
    }
  }

  // Create access action.
  if ($action == 'create') {
    if (!isset($_REQUEST['recipient'])) {
      $_SESSION['message'] = 'Invalid recipient';
      header("Location: /conversations/access.php?id=$id");
      exit;
    }
    else {
      $email = $_REQUEST['recipient'];
    }

    $uid = getUserIDByEmail($email);
    if (!$uid) {
      $_SESSION['message'] = "No recipient account was found at that address.";
      header("Location: /conversations/access.php?id=$id");
      exit;
    }

    $_SESSION['message'] = "Adding user... ";

    if (createAccess($id, $uid)) {
      $_SESSION['message'] .= "Access granted.";
      header("Location: /conversations/access.php?id=$id");
      exit;
    }
    else {
      $_SESSION['message'] .= "Failed to grant access.";
      header("Location: /conversations/access.php?id=$id");
      exit;
    }
  }

  $_SESSION['message'] = "Invalid action";
  header("Location: /conversations/access.php?id=$id");
  exit;
