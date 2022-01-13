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

      $head = getHtmlHeader(['title' => 'Search']);
      $header = getHeader($user);
      $sidebar = getSidebar($user, "search");
      $sidebar2 = getSidebar2($user);
      $form = getSearchForm($q);
      $content = searchPosts($q);
  }
?>
<html>
<head>
<?php print $head; ?>
<script type="text/javascript" src="search.js"></script>
</head>
<body class="search">
  <?php print $header; ?>
  <div class="wrapper">
    <?php print $sidebar; ?>
    <div id="content">

      <?php print $form; ?>

      <?php if (sizeof($content) == 0): ?>
        <?php if (!empty($q)) print "No results"; ?>
        <?php print getDashboard($user); ?>
      <?php else: ?>
        <?php print sizeof($content); ?> results:
        <ul id="search-results">
        <?php foreach ($content as $post): ?>
          <li>
            <a href="/conversations/post.php?id=<?php print $post['parent_id'] ? $post['parent_id'] : $post['id']; ?>">
              <?php print $post['title']; ?></a>
            <span class="time-ago">
              created <?php print time_ago($post['created']); ?>
              updated <?php print time_ago($post['updated']); ?>
            </span>
            <br />
            <?php print $post['body']; ?>
          </li>
        <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
    <?php print $sidebar2; ?>
  </div>
</body>
</html>
