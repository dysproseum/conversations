<?php
  // Ensure logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('database.php');
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('Location: /conversations/login.php');
      exit;
    }
    else {
      require_once('template.php');
      $head = getHtmlHeader(['title' => 'New Topic']);
      $header = getHeader($user);
      $sidebar = getSidebar($user, "search");
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
</body>
</html>
