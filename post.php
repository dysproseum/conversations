<?php
  // Ensure valid post id.
  $id = (int) $_GET['id'];
  if (!$id) {
    header('HTTP/1.1 404 Not Found');
    print "Invalid post id";
    exit;
  }

  global $link;
  global $user;

  // Ensure logged in user.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('database.php');
    $user = getUserInfo($_SESSION['sub']);
    if (!$user) {
      header('Location: /conversations/login.php');
      exit;
    }
    else {
      // Check access.
      $access = FALSE;
      $query = "SELECT uid FROM access WHERE id = " . $id;
      $result = mysqli_query($link, $query);
      foreach ($result as $row) {
        $access = TRUE;
      }

      require_once('template.php');
      $post = []; 
      if ($access) {
        $post = getPost($id);
        if (!$post) {
          header('HTTP/1.1 404 Not Found');
          print "Invalid post ID";
          exit;
        }
      }
      else {
        header('HTTP/1.1 403 Forbidden');
        exit;
      }

      $header = getHeader($user);
      $sidebar = getSidebar($user);
      $content = viewPost($post);
      $form = getPostCommentForm($user, $post);
      $comments = getPostComments($id);
    }
  }
?>

<html>
<head>
<script type="text/javascript" src="post.js"></script>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="post">
  <div id="header"><?php print $header; ?></div>
  <div id="sidebar"><?php print $sidebar; ?></div>
  <div id="content">
    <?php print $content; ?>

    <div id="chat">
      <?php foreach ($comments as $comment): ?>
        <div class="comment-wrapper">
          <div class="comment <?php if ($comment['uid'] == $user->id) print "me"; ?>">
            <img class="avatar-small" src="<?php print $comment['picture']; ?>" alt="user avatar"
              title="<?php print $comment['name']; ?> <?php print date('Y-m-d h:ia', $comment['created']); ?>" />
            <?php if ($comment['body']): ?>
              <p><?php print $comment['body']; ?></p>
            <?php endif; ?>
            <?php if ($comment['link']): ?>
              <p><a href="<?php print $comment['link']; ?>"><?php print $comment['link']; ?></a></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php print $form; ?>
    
  </div>
</body>
</html>
