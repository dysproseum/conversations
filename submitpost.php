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
  $stmt = $mysqli->prepare("INSERT INTO posts (uid, created, link, body) VALUES (?, ?, ?, ?)");
  $stmt->bind_param('iiss',
    $user->id,
    $time,
    $post['link'],
    $post['body']
  );
  $stmt->execute();
  $stmt->close();
  $id = $mysqli->insert_id;

  // Get user id for recipient.
  // @todo Handle case where recipient user id does not exist yet
  $recipient = $post['recipient'];
  $stmt = $mysqli->prepare("SELECT id FROM users WHERE email like ? LIMIT 1");
  $stmt->bind_param('s', $recipient);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if (!$result) {
    print $query;
    print "database error 2";
    exit;
  }
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

