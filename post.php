<?php
  // Ensure valid post id.
  $id = (int) $_GET['id'];
  if (!$id) {
    header('HTTP/1.1 404 Not Found');
    print "Invalid post id";
    exit;
  }

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
    global $user;
    if (!$user) {
      header('Location: /conversations/login.php');
      exit;
    }
    else {
      // Check access.
      $access = FALSE;
      $stmt = $mysqli->prepare("SELECT uid FROM access WHERE id = ?");
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
      foreach ($result as $row) {
        $access = TRUE;
      }

      require_once('template.php');
      $post = [];
      if ($access) {
        $post = getPost($id);
        if (!$post) {
          header('HTTP/1.1 404 Not Found');
          print "Invalid post ID";
          exit;
        }
      }
      else {
        header('HTTP/1.1 403 Forbidden');
        exit;
      }

      $header = getHeader($user);
      $sidebar = getSidebar($user, $id);
      $content = viewPost($post);
      $form = getPostCommentForm($user, $post);
      $comments = getPostComments($id);
      $last_comment = getLastComment($id);
      $last_id = $last_comment['id'];
    }
  }
?>

<html>
<head>
<script type="text/javascript">
  postId = <?php print $id; ?>;
  commentId = <?php print $last_id; ?>;
</script>
<script type="text/javascript" src="ping.js"></script>
<script type="text/javascript" src="post.js"></script>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="post">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">
      <?php print $content; ?>

      <div id="chat">
        <?php foreach ($comments as $comment): ?>
          <div class="comment-wrapper <?php if ($comment['uid'] == $user->id) print "me"; ?>">
            <div class="comment <?php if ($comment['uid'] == $user->id) print "me"; ?>">
              <img class="avatar-small" src="<?php print $comment['picture']; ?>" alt="user avatar"
                title="<?php print $comment['name']; ?> <?php print date('Y-m-d H:i', $comment['created']) . " UTC"; ?>" />
              <?php if ($comment['body']): ?>
                <p><?php print nl2br($comment['body']); ?></p>
              <?php endif; ?>
              <?php if ($comment['link']): ?>
                <p><a target="_blank" href="<?php print $comment['link']; ?>"><?php print $comment['link']; ?></a></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php print $form; ?>

    </div>
  </div>
</body>
</html>
