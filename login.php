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
  $foot = getHtmlFooter();
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
          <br>
          <?php print $user->email; ?>
        </p>

        <p>
          <label for="notify_enable"></label>
          <button class="submit-button" id="notify_enable">
            Enable notifications
          </button>
        </p>
        <p>
          <label for="notify">Banner notifications</label>
          <?php if ($user->notify == 1): ?>
            <script type="text/javascript">
              var notify = 1;
            </script>
          <?php else: ?>
            <script type="text/javascript">
              var notify = 0;
            </script>
          <?php endif; ?>
        </p>
        <p>
          <input type="radio" id="notify_banner_2" value="2" name="notify_banner" disabled />
          <label for="notify_banner_2">All messages</label>
        </p>
        <p>
          <input type="radio" id="notify_banner_1" value="1" name="notify_banner" disabled />
          <label for="notify_banner_1">New topics only</label>
        </p>
        <p>
          <input type="radio" id="notify_banner_0" value="0" name="notify_banner" disabled />
          <label for="notify_banner_0">Nothing</label>
        </p>

        <label for="notifysound">Notification Sounds</label>
        <p>
          <input type="radio" id="notify_sound_2" value="2" name="notify_sound" disabled />
          <label for="notify_sound_2">Always</label>
        </p>
        <p>
          <input type="radio" id="notify_sound_1" value="1" name="notify_sound" disabled />
          <label for="notify_sound_1">Inactive posts only</label>
        </p>
        <p>
          <input type="radio" id="notify_sound_0" value="0" name="notify_sound" disabled />
          <label for="notify_sound_0">Never</label>
        </p>
        <p><button id="notify_test" class="submit-button" disabled>Test Notifications</button></p>

        <p><a href="#" onclick="signOut();">Sign out</a></p>

      <?php else: ?>

        <p id="user-instructions">Click "Sign in" to login with your Google account. This site stores no passwords or personal information.</p>

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

      <?php endif; ?>

    </div>
    <?php print $sidebar2; ?>
  </div>
  <?php print $foot; ?>
</body>
</html>
