<?php
  // Get search term if exists.
  $q = $_GET['q'];
  if (!$q) {
    $q = '';
  }

  // Ensure logged in user.
  session_start();
  if (!isset($_SESSION['sub'])) {
    header('Location: /conversations/login.php');
    exit;
  }
  else {
    require_once('database.php');
    global $mysqli;

    $user = getUserInfo($_SESSION['sub']);
    global $user;

      require_once('template.php');

      $header = getHeader($user);
      $sidebar = getSidebar($user);
      $form = getSearchForm($q);
      $content = searchPosts($q);
  }
?>

<html>
<head>
<title>Search</title>
<script type="text/javascript" src="post.js"></script>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="post">
  <div id="header"><?php print $header; ?></div>
  <div class="sidebar"><?php print $sidebar; ?></div>
  <div id="content">
    <?php print $form; ?>

    <?php if (sizeof($content) == 0): ?>
      No results
    <?php endif; ?>
    <ul>
    <?php foreach ($content as $post): ?>
      <li>
        <a href="/conversations/post.php?id=<?php print $post['parent_id'] ? $post['parent_id'] : $post['id']; ?>">
          <?php print $post['title']; ?>
        </a>
        <span class="time-ago">
          created <?php print time_ago($post['creation']); ?>
          updated <?php print time_ago($post['created']); ?>
        </span>
        <br />
        <?php print $post['body']; ?>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
</body>
</html>
