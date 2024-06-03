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

  require_once('include/database.php');
  $user = getUserInfo($_SESSION['sub']);
  if (!$user) {
    header('Location: /conversations/login.php');
    exit;
  }

  $post = getPost($id);
  if (!$post) {
    header('HTTP/1.1 404 Not Found');
    print "Invalid post ID";
    exit;
  }

  if (!checkAccess($post, $user)) {
    header('HTTP/1.1 403 Forbidden');
    exit;
  }

  require_once('include/template.php');
  $head = getHtmlHeader(['title' => $post['body']]);
  $header = getHeader($user);
  $sidebar = getSidebar($user, $id);
  $sidebar2 = getSidebar2($user);
  $content = viewPost($post);
  $form = getPostCommentForm($user, $post);
  $comments = getPostComments($id);
  $last_comment = getLastComment($id);
  $last_id = $last_comment['id'];
  $current_img = '';
  $current_day = '';
?>
<!DOCTYPE HTML>
<html>
<head>
  <?php print $head; ?>
  <script type="text/javascript">
    const postId = '<?php print $id; ?>';
    var commentId = '<?php print $last_id; ?>';
  </script>
</head>
<body class="post">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">
      <?php print $content; ?>

      <div id="chat">
        <?php foreach ($comments as $comment): ?>
            <?php print buildComment($comment, $current_img, $current_day); ?>
        <?php endforeach; ?>
      </div>

      <?php print $form; ?>

    </div>
    <?php print $sidebar2; ?>
  </div>
</body>
</html>
