<?php
  // Ensure logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('include/database.php');
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('Location: /conversations/login.php');
      exit;
    }
    else {
      require_once('include/template.php');
      $head = getHtmlHeader(['title' => 'New Topic']);
      $foot = getHtmlFooter();
      $header = getHeader($user);
      $sidebar = getSidebar($user, "new");
      $sidebar2 = getSidebar2($user, "new");
      $content = getNewPostForm($user);
    }
  }
?>

<html>
<head>
<?php print $head; ?>
</head>
<body class="new post">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">
      <?php print $content; ?>
    </div>
    <?php print $sidebar2; ?>
  </div>
  <?php print $foot; ?>
</body>
</html>
