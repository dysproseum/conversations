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
    }
  }
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
  <div id="header"><?php print $header; ?></div>
  <div id="sidebar"><?php print $sidebar; ?></div>
  <div id="content"><?php print $content; ?></div>
</body>
</html>
