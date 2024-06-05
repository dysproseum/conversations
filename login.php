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
  require_once('include/database.php');
  $sub = isset($_SESSION['sub']) ? true : false;
  $user = getUserInfo($_SESSION['sub']);
  $message = $_SESSION['message'];
  unset($_SESSION['message']);

  // Note: Update user picture on login.
  // The image URL is passed by login.js upon sign in.
  // updatePicture() is then called from tokensignin.php.

  require_once('include/template.php');
  $head = getHtmlHeader(['title' => 'Login']);
  $header = getHeader($user);
  $sidebar = getSidebar($user);
  $sidebar2 = getSidebar2($user);
?>
<!DOCTYPE HTML>
<html>
<head>
  <?php print $head; ?>
  <script type="text/javascript" src="js/login.js"></script>
</head>

<body class="login">
  <script src="https://accounts.google.com/gsi/client" async defer></script>

  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">

      <h1 id="contentheader">Login</h1>
      <p id="user-instructions">Click "Sign in" to login with your Google account. This site stores no passwords or personal information.</p>

      <span id="user-message" /><?php print $message; ?></span>

      <div id="g_id_onload"
        data-client_id="<?php print CLIENT_ID; ?>"
        data-callback="handleCredentialResponse">
      </div>
      <div class="g_id_signin" data-type="standard"></div>

      <p id="user-continue" <?php if (!$sub) print "hidden"; ?>>
        <a href="#" onclick="signOut();">Sign out</a>
        <a id="submit-button" href="/conversations/dashboard.php">Continue to Dashboard</a>
      </p>
    </div>
    <?php print $sidebar2; ?>
  </div>
  <?php if (file_exists('include/analytics.html')): ?>
  <?php include('include/analytics.html'); ?>
  <?php endif; ?>
</body>
</html>
