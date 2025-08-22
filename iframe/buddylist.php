<?php
  // Ensure logged in user.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }

chdir("..");
  require_once('include/database.php');
  $user = getUserInfo($_SESSION['sub']);
  if (!$user) {
    header('Location: /conversations/login.php');
    exit;
  }

  require_once('include/template.php');
  $head = getHtmlHeader(['title' => 'Buddy List']);
  $foot = getHtmlFooter();
  $header = getHeader($user);
  $sidebar = getSidebar($user, $id);
  $sidebar2 = getSidebar2($user);
  $content = viewPost($post);
  //$form = getPostCommentForm($user, $post);
  //$comments = getPostComments($id);
  //$last_comment = getLastComment($id);
  //$last_id = $last_comment['id'];
  //$last_comments = getLatestComments($last_id);
  // Prevent notifications on page load.
  //if (isset($last_comments[0])) {
  //  $last_id = $last_comments[0]['id'];
  //}
  $current_img = '';
  $current_day = '';
?>
<!DOCTYPE HTML>
<html>
<head>
  <?php print $head; ?>
  <script type="text/javascript" src="override.js"></script>
  <script type="text/javascript">
    //var postId = '<?php print $id; ?>';
  </script>
  <link rel="stylesheet" type="text/css" href="override.css" media="screen">
</head>
<body class="iframe">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar2; ?>
  </div>
  <?php print $foot; ?>
</body>
</html>
