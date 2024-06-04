<?php

  // Ensure valid post id.
  $id = (int) $_REQUEST['id'];
  if (!$id) {
    header('HTTP/1.1 404 Not Found');
    print "Invalid post id";
    exit;
  }

  require_once('config.php');
  session_start();
  $sub = isset($_SESSION['sub']) ? true : false;
  if (!$sub) {
    header('HTTP/1.1 403 Forbidden');
    exit;
  }

  require_once('include/database.php');
  $post = getPost($id);
  $user = getUserInfo($_SESSION['sub']);
  $uids = getPostAccess($id);
  $message = $_SESSION['message'];
  unset($_SESSION['message']);

  require_once('include/template.php');
  $head = getHtmlHeader(['title' => 'Delete']);
  $header = getHeader($user);
  $sidebar = getSidebar($user);
  $sidebar2 = getSidebar2($user);
?>

<html>
<head>
  <?php print $head; ?>
</head>

<body class="access">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">

      <h1 id="contentheader">Access</h1>
      
      <p id="user-instructions">
        Are you sure you want to delete this post?
        <a href="/conversations/post.php?id=<?php print $id; ?>">Cancel</a>
      </p>
      <p>
        Manage access to this post:
        <br>
        <strong><?php print $post['body']; ?></strong>
      </p>

      <span id="user-message" /><?php print $message; ?></span>

      <?php foreach ($uids as $uid): ?>
        <?php $u = getUser($uid); ?>
        <form method="post" action="/conversations/manage_access.php">
          <?php print $u->name; ?>
          <?php if ($u->id == $user->id): ?>
            (me)
          <?php endif; ?>
          <?php if ($u->id == $post['uid']): ?>
            (author)
          <?php endif; ?>
          <input type="hidden" name="action" value="delete" />
          <input type="hidden" name="id" value="<?php print $id; ?>" />
          <input type="hidden" name="uid" value="<?php print $uid; ?>" />
          <input type="submit" class="submit-button" value="Remove" />
        </form>
      <?php endforeach; ?>

      <form method="post">
        <input type="hidden" name="action" value="create" />
        <input type="hidden" name="id" value="<?php print $id; ?>" />
        <label for="recipient">Add recipient</label>
        <input type="text" id="recipient" placeholder="E-mail address" />
        <input type="submit" class="submit-button" value="Add" />
      </form>
    </div>
    <?php print $sidebar2; ?>
  </div>
  <?php if (file_exists('include/analytics.html')): ?>
  <?php include('include/analytics.html'); ?>
  <?php endif; ?>
</body>
</html>
