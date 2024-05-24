<?php

  // Ensure logged in user.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('include/database.php');
    global $mysqli;

    $user = getUserInfo($_SESSION['sub']);
    global $user;

      require_once('include/template.php');
      $head = getHtmlHeader(['title' => 'Reports']);
      $header = getHeader($user);
      $sidebar = getSidebar($user, "reports");
      $sidebar2 = getSidebar2($user);
      $content = printReport();
  }
?>

<html>
<head>
<?php print $head; ?>
</head>
<body class="post">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">

      <h1 id="contentheader">System Reports</h1>
      Total Posts: <?php print $content['num_posts']; ?><br>
      Total Comments: <?php print $content['num_comments']; ?><br>
      Total Users: <?php print $content['num_users']; ?><br>
    </div>
    <?php print $sidebar2; ?>
  </div>
</body>
</html>
