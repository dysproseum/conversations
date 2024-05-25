<?php
  // Ensure Google client id exists.
  require_once('config.php');
  if (isset($conf['google']) && isset($conf['google']['client_id'])) {
    define('CLIENT_ID', $conf['google']['client_id']);
  }
  else {
    header('HTTP/1.1 500 Server Error');
    print "Google client id not found";
    exit;
  }

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
  <script type="text/javascript" src="js/login.js"></script>
</head>
<body class="home">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <div id="header">
    <h1>conversations</h1>
    alpha
    <span id="footer">
      <a href="https://github.com/dysproseum/conversations" target="_blank">conversations on github</a>
    </span>
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
      <p>&nbsp;</p>

      <div id="g_id_onload"
           data-client_id="<?php print CLIENT_ID; ?>"
           data-context="signin"
           data-ux_mode="popup"
           data-callback="handleCredentialResponse"
           data-auto_prompt="false">
      </div>
      <div class="g_id_signin"
           data-type="standard"
           data-shape="pill"
           data-theme="filled_blue"
           data-text="signin_with"
           data-size="large"
           data-logo_alignment="left"
           data-width="300px">
      </div>

    </div>
  </div>
  <?php if (file_exists('include/analytics.html')): ?>
  <?php include('include/analytics.html'); ?>
  <?php endif; ?>
</body>
</html>
