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
  $head = getHtmlHeader(['title' => 'My Account']);
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
      <h1 id="contentheader">My Account</h1>

      <span id="user-message" /><?php print $message; ?></span>

      <?php if ($sub): ?>

        <p id="user-continue">Signed in as:</p>
        <p>
          <img class="avatar-small-left" src="<?php print $user->picture; ?>" />
          <?php print $user->name; ?>
          <a id="submit-button" href="/conversations/dashboard.php">Continue to Dashboard</a>
          <br>
          <?php print $user->email; ?>
        </p>
        <p><a href="#" onclick="signOut();">Sign out</a></p>

      <?php else: ?>

        <p id="user-instructions">Click "Sign in" to login with your Google account. This site stores no passwords or personal information.</p>
        <div id="g_id_onload"
          data-client_id="<?php print CLIENT_ID; ?>"
          data-callback="handleCredentialResponse">
        </div>
        <div class="g_id_signin" data-type="standard"></div>

      <?php endif; ?>

    </div>
    <?php print $sidebar2; ?>
  </div>
  <?php if (file_exists('include/analytics.html')): ?>
  <?php include('include/analytics.html'); ?>
  <?php endif; ?>
</body>
</html>
