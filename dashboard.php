<?php
  // Ensure user logged in.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/index.php');
    exit;
  }
  else {
    require_once('include/database.php');
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('Location: /conversations/index.php');
      exit;
    }
    else {
      require_once('include/template.php');
      $head = getHtmlHeader(['title' => 'Dashboard']);
      $foot = getHtmlFooter();
      $header = getHeader($user);
      $sidebar = getSidebar($user, "dashboard");
      $content = getDashboard($user);
      $sidebar2 = getSidebar2($user);
    }
  }
?>

<html>
<head>
<?php print $head; ?>
</head>
<body class="dashboard">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <?php print sessionMessage(); ?>
    <?php if ($content): ?>
      <?php print $content; ?>
    <?php endif; ?>
    <?php print $sidebar2; ?>
  </div>
  <?php print $foot; ?>
</body>
</html>
