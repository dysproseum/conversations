<?php
  // Ensure user logged in.
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
      $header = getHeader($user);
      $sidebar = getSidebar($user);
      $content = getDashboard($user);
      $sidebar2 = getSidebar2($user);
    }
  }
?>

<html>
<head>
<script type="text/javascript" src="fullscreen.js"></script>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
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
