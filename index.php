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
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="home">
  <div id="header">
    <h1>conversations</h1>
    <div class="profile-block">
      <a href="login.php">Login with Google</a>
    </div>
  </div>
  <div class="wrapper">
    <div class="sidebar">
      <ul></ul>
    </div>
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

      <a href="login.php">Login with Google</a>

    </div>
  </div>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-4383228-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-4383228-1');
</script>


</body>
</html>
