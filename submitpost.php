<?php
  // Ensure logged in user.
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

  // Get user id for recipient.
  $recipient = $post['recipient'];
  $stmt2 = $mysqli->prepare("SELECT id FROM users WHERE email like ? LIMIT 1");
  $stmt2->bind_param('s', $recipient);
  $stmt2->execute();
  $result = $stmt2->get_result();
  if (!$result) {
    $_SESSION['message'] = "Error retrieving user id for recipient (database error 2)";
    header('Location: /conversations/new.php');
    exit;
  }
  else {
    // @todo Handle case where recipient user id does not exist yet
    // Invite user?
    //$_SESSION['message'] = "No recipient account was found at that address.";
    //header('Location: /conversations/new.php');
    //exit;
  }
  $stmt2->close();

  foreach ($result as $row) {
    $uid = $row['id'];
  }

  // Set up access for OP and recipient.
  $stmt = $mysqli->prepare("INSERT INTO access (id, uid) VALUES (?, ?), (?, ?)");
  $stmt->bind_param('iiii', $id, $uid, $id, $user->id);
  $stmt->execute();
  $stmt->close();

  // @todo Notifications
  // Email user an invite if they don't exist
  // Browser notification for user that does exist

  // Redirect to post.
  header('Location: /conversations/post.php?id=' . $id);
  exit;

