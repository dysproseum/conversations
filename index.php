<?php
  session_start();
  if (isset($_SESSION['sub'])) {
    header('Location: /conversations/dashboard.php');
    exit;
  }
?>

<html>
<head>
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="home">
  <div id="content">
    <h1>Hello</h1>
    Welcome to Conversations! <a href="login.php">Login with Google</a>
  </div>
</body>
</html>
