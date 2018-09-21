<?php
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
      $content = getDashboard($user);
    }
  }
}
?>

<html>
<head>

</head>
<body>
  <div id="header"><?php print $header; ?></div>
  <div id="content"><?php print $dashboard; ?></div>
</body>
</html>
