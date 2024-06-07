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

  // Create new post in database.
  $post = $_POST;
  $time = time();
  if (empty($post['link']) && empty($post['body'])) {
    $_SESSION['message'] = 'Empty link and/or body.';
    header('Location: /conversations/new.php');
    exit;
  }
  $body = strip_tags($post['body']);
  if (!empty($post['link'])) {
    $url = filter_var($post['link'], FILTER_VALIDATE_URL);
    if (!$url) {
      $_SESSION['message'] = 'Invalid URL';
      header('Location: /conversations/new.php');
      exit;
    }
  }

  $stmt = $mysqli->prepare("INSERT INTO posts (uid, created, link, body) VALUES (?, ?, ?, ?)");
  $stmt->bind_param('iiss',
    $user->id,
    $time,
    $post['link'],
    $body,
  );
  $stmt->execute();
  $stmt->close();
  $id = $mysqli->insert_id;


  // Set up access for OP.
  if (!createAccess($id, $user->id)) {
    $_SESSION['message'] = "Error creating access for OP.";
  }

  // Get user id for recipient.
  if (isset($post['recipient'])) {
    $recipient = $post['recipient'];
    $uid = getUserIDByEmail($recipient);
    if (!$uid) {
      // @todo Notifications
      // Email user an invite if they don't exist
      // Browser notification for user that does exist

      $_SESSION['message'] = "No recipient account was found at that address.";
    }
  }

  // Set up access for recipient.
  if ($uid) {
    if (!createAccess($id, $uid)) {
      $_SESSION['message'] = "Error creating access for recipient.";
    }
  }

  // Redirect to post.
  header('Location: /conversations/post.php?id=' . $id);
  exit;
