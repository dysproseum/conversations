<?php
  // Redirect to dashboard if logged in.
  session_start();
  if (isset($_SESSION['sub'])) {
    header('Location: /conversations/dashboard.php');
    exit;
  }
?>

<html>
<head>
  <title>Conversations | Open-source chat service</title>

  <link rel="stylesheet" type="text/css" href="css/styles.css" media="screen">
  <link rel='stylesheet' media='only screen and (max-width: 768px)' href='css/mobile.css' type='text/css' />

  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0" />

</head>
<body class="home">
  <div id="header">
    <h1>conversations</h1>
    alpha
  </div>
  <div class="wrapper">
    <div id="content">
      <h1>Hello</h1>
      Welcome to Conversations!
      <br>
      <h2>A lightweight open-source correspondence platform</h2>
      <ul>
        <li>Have a real conversation with someone (or yourself)</li>
        <li>Manage topics in message threads</li>
        <li>Search everything easily from your phone or computer</li>
      </ul>
      <br>
      <h2>Privacy and Security</h2>
      <ul>
        <li>No personal account information is stored</li>
        <li>Delete messages at any time</li>
        <li>Run your own instance</li>
      </ul>

      <a id="submit-button" href="login.php">Login with Google</a>

    </div>
  </div>
  <?php if (file_exists('include/analytics.html')): ?>
  <?php include('include/analytics.html'); ?>
  <?php endif; ?>
</body>
</html>
