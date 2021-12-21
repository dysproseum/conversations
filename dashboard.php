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
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
  <div id="header"><?php print $header; ?></div>
  <div class="sidebar"><?php print $sidebar; ?></div>
  <div id="content"><?php print $content; ?></div>
  <div class="sidebar" id="sidebar2"><?php print $sidebar2; ?></div>
</body>
</html>
