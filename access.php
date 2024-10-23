<?php

  // Ensure valid post id.
  $id = (int) $_REQUEST['id'];
  if (!$id) {
    header('HTTP/1.1 404 Not Found');
    print "Invalid post id";
    exit;
  }

  session_start();
  $sub = isset($_SESSION['sub']) ? true : false;
  if (!$sub) {
    header('HTTP/1.1 403 Forbidden');
    exit;
  }

  require_once('include/database.php');
  $post = getPost($id);
  $user = getUserInfo($_SESSION['sub']);

  if (!checkAccess($post, $user)) {
    header('HTTP/1.1 403 Forbidden');
    exit;
  }

  $uids = getPostAccess($id);

  require_once('include/template.php');
  $head = getHtmlHeader(['title' => 'Access']);
  $foot = getHtmlFooter();
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
      <?php print sessionMessage(); ?>
      
      <p id="user-instructions">
          Click here to delete this post:
          <strong><?php print $post['body']; ?></strong>
          <form method="post" action="/conversations/manage_access.php">
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="id" value="<?php print $id; ?>" />
            <?php if ($user->id == $post['uid']): ?>
              <input type="submit" class="submit-button" value="Delete" />
            <?php else: ?>
              <input type="submit" class="submit-button" value="Delete" disabled="disabled" />
            <?php endif; ?>
            <a href="/conversations/post.php?id=<?php print $id; ?>">Cancel</a>
          </form>
      </p>

       <p>Add recipient:</p>
      <form method="post" action="/conversations/manage_access.php">
        <input type="hidden" name="action" value="create" />
        <input type="hidden" name="id" value="<?php print $id; ?>" />
        <input type="text" id="recipient" name="recipient" placeholder="E-mail address" />
        <input type="submit" class="submit-button" value="Add" />
      </form>

      <p>Users with access to this post:</p>
      <?php foreach ($uids as $uid): ?>
        <?php $u = getUser($uid); ?>
        <form method="post" action="/conversations/manage_access.php">
          <input type="hidden" name="action" value="remove" />
          <input type="hidden" name="id" value="<?php print $id; ?>" />
          <input type="hidden" name="uid" value="<?php print $uid; ?>" />
          <img class="avatar-small-left" src="<?php print $u->picture; ?>" alt="user avatar" />
          <span class="user-label">
            <?php print $u->name; ?>
            <?php if ($u->id == $user->id): ?>
              (me)
            <?php endif; ?>
            <?php if ($u->id == $post['uid']): ?>
              (author)
            <?php endif; ?>
          </span>
          <?php if ($user->id == $post['uid'] && $user->id == $u->id): ?>
            <input type="submit" class="submit-button" value="Leave" disabled="disabled" />
          <?php elseif ($user->id == $post['uid']): ?>
            <input type="submit" class="submit-button" value="Remove" />
          <?php elseif ($user->id == $u->id): ?>
            <input type="submit" class="submit-button" value="Leave" />
          <?php else: ?>
            <input type="submit" class="submit-button" value="Remove" disabled="disabled" />
          <?php endif; ?>
        </form>
      <?php endforeach; ?>

    </div>
    <?php print $sidebar2; ?>
  </div>
  <?php print $foot; ?>
</body>
</html>
