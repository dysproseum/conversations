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
  session_start();
  require_once('database.php');
  $user = getUserInfo($_SESSION['sub']);

  // @todo Update user picture on login
  require_once('template.php');
  $header = getHeader($user);
  $sidebar = getSidebar($user);
?>

<html>
<head>
  <script src="https://apis.google.com/js/platform.js" async defer></script>
  <script type="text/javascript" src="login.js"></script>
  <meta name="google-signin-client_id" content="<?php print CLIENT_ID; ?>">
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body class="login">
  <div id="header"><?php print $header; ?></div>
  <div class="sidebar"><?php print $sidebar; ?></div>
  <div id="content">
    <h1>Login</h1>
    <p><span id="user-message" /></p>

    <?php /* https://developers.google.com/identity/sign-in/web/sign-in */ ?>

    <div class="g-signin2" data-onsuccess="onSignIn"></div>
    <p><a href="#" onclick="signOut();">Sign out</a></p>
    
    <p id="user-continue" hidden >
      Logged in from the backend:
      <a href="/conversations/dashboard.php">Continue to Dashboard</a>
    </p>

  </div>
</body>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-4383228-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-4383228-1');
</script>


</html>
