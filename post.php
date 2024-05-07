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
    require_once('include/database.php');
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

      require_once('include/template.php');
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
    }
  }
?>
<!DOCTYPE HTML>
<html>
<head>
<script type="text/javascript">
  postId = '<?php print $id; ?>';
  commentId = '<?php print $last_id; ?>';
</script>

<?php print $head; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0" />
</head>
<body class="post">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">
      <?php print $content; ?>

      <div id="chat">
        <?php foreach ($comments as $comment): ?>
          <?php $imgs = getImagesLinks($comment['body']); ?>
          <?php $body = displayTextWithLinks(nl2br($comment['body'])); ?>
          <?php $timestamp = $comment['name'] . date(' Y-m-d H:i', $comment['created']) . " UTC"; ?>
          <?php $cid = $comment['post_id']; ?>
          <?php $permalink = '?id=' . $post['id'] . '&cid=' . $cid; ?>

          <?php if (($current_img !== $comment['picture']) || ($current_day !== date('d', $comment['created']))): ?>

            <?php $current_img = $comment['picture']; ?>
            <?php $current_day = date('d', $comment['created']); ?>
            <div class="comment-wrapper current" id="<?php print $cid; ?>">
              <div class="comment <?php if ($comment['uid'] == $user->id) print "me"; ?>">
                <img class="avatar-small current" src="<?php print $current_img; ?>" alt="user avatar" title="<?php print $timestamp; ?>" align="left" />

		<a class="permalink" title="<?php print $timestamp; ?>" href="<?php print $permalink; ?>">Permalink</a>

          <?php else: ?>
            <div class="comment-wrapper current" id="<?php print $cid; ?>">
              <div class="comment <?php if ($comment['uid'] == $user->id) print "me"; ?>">
                <img class="avatar-small" src="images/transparent.gif" align="left" title="<?php print $timestamp; ?>" />

		<a class="permalink" title="<?php print $timestamp; ?>" href="<?php print $permalink; ?>">Permalink</a>

          <?php endif; ?>

          <?php if ($comment['body']): ?>
             <p><?php print $body; ?></p>
          <?php endif; ?>

          <?php if ($imgs): ?>
            <?php foreach ($imgs[0] as $img): ?>
                <p><a target="_blank" href="<?php print $img; ?>">
                  <img class="comment-preview-thumb" src="<?php print $img; ?>" />
                </a></p>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if ($comment['link']): ?>
            <p><a target="_blank" href="<?php print $comment['link']; ?>"><?php print $comment['link']; ?></a></p>
          <?php endif; ?>

              </div> <!-- comment -->
            </div> <!-- comment-wrapper -->
        <?php endforeach; ?>
      </div>

      <?php print $form; ?>

    </div>
    <?php print $sidebar2; ?>
  </div>
</body>
</html>
