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
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">
      <?php print $content; ?>
    </div>
  </div>
</body>
</html>
