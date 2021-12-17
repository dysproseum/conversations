<?php
  // Get search term if exists.
  $q = (int) $_GET['q'];
  if (!$q) {
    $q = '';
  }

  // Ensure logged in user.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('database.php');
    global $mysqli;

    $user = getUserInfo($_SESSION['sub']);
    global $user;

      require_once('template.php');

      $header = getHeader($user);
      $sidebar = getSidebar($user);
      $form = getSearchForm($q);
      //$content = searchPosts($q);
  }
?>

<html>
<head>
<script type="text/javascript" src="post.js"></script>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="post">
  <div id="header"><?php print $header; ?></div>
  <div id="sidebar"><?php print $sidebar; ?></div>
  <div id="content">
    <?php print $form; ?>
  </div>
</body>
</html>
