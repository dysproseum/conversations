<?php

  // Ensure logged in user.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('include/database.php');
    $user = getUserInfo($_SESSION['sub']);
    global $user;

      require_once('include/template.php');
      $head = getHtmlHeader(['title' => 'Reports']);
      $foot = getHtmlFooter();
      $header = getHeader($user);
      $sidebar = getSidebar($user, "reports");
      $sidebar2 = getSidebar2($user);
      $content = generateReport();
  }
?>

<html>
<head>
<?php print $head; ?>
</head>
<body class="reports">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">
      <h1 id="contentheader">System Reports</h1>

      Total Posts: <?php print $content['num_posts']; ?><br>
      Total Comments: <?php print $content['num_comments']; ?><br>
      Total Users: <?php print $content['num_users']; ?><br>
      <br>
      Abandoned Posts: <?php print sizeof($content['posts_no_access']); ?><br>
      <pre>
      <?php foreach ($content['posts_no_access'] as $na): ?>
        <?php print $na . PHP_EOL; ?>
      <?php endforeach; ?>
      </pre>
      Stray Access: <?php print sizeof($content['access_no_posts']); ?><br>
      <pre>
      <?php foreach ($content['access_no_posts'] as $na): ?>
        <?php print $na . PHP_EOL; ?>
      <?php endforeach; ?>
      </pre>
      Stray Comments: <?php print sizeof($content['comment_no_posts']); ?><br>
      <pre>
      <?php foreach ($content['comment_no_posts'] as $na): ?>
        <?php print $na . PHP_EOL; ?>
      <?php endforeach; ?>
      </pre>

    </div>
    <?php print $sidebar2; ?>
  </div>
  <?php print $foot; ?>
</body>
</html>
