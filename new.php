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
      $header = getHeader($user);
      $sidebar = getSidebar($user, "new");
      $content = getNewPostForm($user);
    }
  }
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="new post">
  <div id="header"><?php print $header; ?></div>
  <div class="sidebar"><?php print $sidebar; ?></div>
  <div id="content"><?php print $content; ?></div>
</body>
</html>
