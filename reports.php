<?php

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
      $sidebar = getSidebar($user, "reports");
      $content = printReport();
  }
?>

<html>
<head>
<script type="text/javascript" src="post.js"></script>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="post">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">

      <h1>System Reports</h1>
      Total Posts: <?php print $content['num_posts']; ?><br>
      Total Comments: <?php print $content['num_comments']; ?><br>
      Total Users: <?php print $content['num_users']; ?><br>
    </div>
  </div>
</body>
</html>
