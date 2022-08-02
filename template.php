<?php

require_once('utils.php');

define('SITE_NAME', 'Conversations ฅ^•ﻌ•^ฅ');

// Global items to place in html head tag.
function getHtmlHeader($options) {
  ob_start(); ?>
  <title><?php print $options['title'] . ' | ' . SITE_NAME; ?></title>
  <script type="text/javascript" src="fullscreen.js"></script>
  <script type="text/javascript" src="ping.js"></script>
  <script type="text/javascript" src="post.js"></script>

  <link rel="stylesheet" type="text/css" href="styles.css" media="screen">
  <link rel='stylesheet' media='only screen and (max-width: 768px)' href='mobile.css' type='text/css' />

  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0" />

  <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme header html.
function getHeader($user) {
return;
  $name = $user ? $user->name : '';
  $img = $user ? $user->picture: 'transparent.gif';

  ob_start(); ?>
  <div id="header">
      <a href="/conversations/search.php" title="Home">Conversations</a>
    <div class="profile-block">
        <a href="/conversations/minimize.html" id="minimize">
          <img src="min-button.png" alt="Minimize" title="Hide" />
        </a>
        <a href="#" onclick="toggleFullscreen(this)" id="maximize">
          <img src="max-button.png" alt="Maximize" title="Fullscreen" style="margin-right: 10px" />
        </a>
        <a href="/conversations/login.php" id="exit">
          <img src="x-icon.png" alt="Exit" title="Logout" />
        </a>
    </div>
  </div>
  <div id="submenu">
    <span>File</span>
    <span>Edit</span>
  </div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme sidebar html.
function getSidebar($user, $this_post = '') {

  $posts = getPostsCreatedByUser($user);
  $sorted = [];
  if (!$posts) {
//    return false;
  }
  else {
    // Re-sort posts by latest comment.
    foreach ($posts as $post) {
      $comment = getLastComment($post['id']);
      $sorted[$comment['created']] = $post;
    }
  }
  krsort($sorted);

  ob_start(); ?>
  <div class="sidebar">
    <ul>
    <?php if ($user): ?>
    <li class="post search <?php if ($this_post == "search") print "active"; ?>">
      <a href="/conversations/search.php">Conversations &#x1F50E;</a>
    </li>
      <li class="post new">
        <a href="/conversations/new.php">New Topic +</a>
      </li>
    <?php foreach ($sorted as $post_id): ?>
      <?php $post = getPost($post_id['id']); ?>
      <?php $comment = getLastComment($post['id']); ?>
      <li class="post <?php if($post['id'] == $this_post) print "active"; ?>">
        <a href="/conversations/post.php?id=<?php print $post['id']; ?>">

          <!-- @todo read/unread status -->

          <?php if (!empty($post['body'])): ?>
            <?php print substr($post['body'], 0, 18); ?>
          <?php elseif (!empty($post['link'])): ?>
            <?php print substr($post['link'], 0, 18); ?>
          <?php else: ?>
            (untitled)
          <?php endif; ?>
          <br />
          <span class="time-ago">
            <?php print $comment ? time_ago($comment['created']) : time_ago($post['created']); ?>
          </span>
          <img class="avatar-small" src="<?php print $comment ? $comment['picture'] : $post['picture']; ?>" alt="user avatar" />
        </a>
      </li>
    <?php endforeach; ?>
      <li class="reports <?php if ($this_post == "reports") print "active"; ?>">
        <a href="/conversations/reports.php">Reports</a>
      </li>
    <?php else: ?>
      <li>
        <a href="/conversations/">Home</a>
      </li>
    <?php endif; ?>
    </ul>
  </div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme sidebar html.
function getSidebar2($user, $this_post = '') {
  global $user;

  $posts = getMyPosts($user);
  $sorted = [];
  if (!$posts) {
//    return false;
  }
  else {
    // Re-sort posts by latest comment.
    foreach ($posts as $post) {
      $comment = getLastComment($post['id']);
      $sorted[$comment['created']] = $post;
    }
  }
  krsort($sorted);

  if ($user->name) {
    $username = $user->name;
  }
  else {
    $username = 'Unknown';
  }
  if ($user->picture) {
    $picture = $user->picture;
  }
  else {
    $picture = 'unknown-user.jpg';
  }


  ob_start(); ?>
  <div class="sidebar" id="sidebar2">
  <ul>
  <li class="active header">
    <a href="#" style="float: left">Recent Topics</a>

        <a href="/conversations/minimize.html" id="minimize">
          <img src="min-button.png" alt="Minimize" title="Hide" />
        </a>
        <a href="#" onclick="toggleFullscreen(this)" id="maximize">
          <img src="max-button.png" alt="Maximize" title="Fullscreen" style="margin-right: 10px" />
        </a>
        <a href="/conversations/login.php" id="exit">
          <img src="x-icon.png" alt="Exit" title="Logout" />
        </a>
  </li>
  <li class="post account">
    <a href="/conversations/login.php">
      <div class="profile-block">
          <img id="user-picture" src="<?php print $picture; ?>" />
          <span id="user-name"><?php print $username; ?></span>
          <br>
          My account
      </div>
    </a>
  </li>

  <!-- @todo read/unread status -->

  <?php foreach ($sorted as $post_id): ?>
    <?php $post = getPost($post_id['id']); ?>
    <?php $comment = getLastComment($post_id['id']); ?>
    <?php $link = "/conversations/post.php?id=" . $post['id'] . "&cid=" . $comment['id']; ?>
    <li class="post <?php if($post_id['id'] == $this_post) print "active"; ?>">
      <a href="<?php print $link; ?>">

        <?php if (!empty($post['body'])): ?>
          <?php print substr($post['body'], 0, 24); ?>
        <?php elseif (!empty($post['link'])): ?>
          <?php print substr($post['link'], 0, 18); ?>
        <?php else: ?>
          (untitled)
        <?php endif; ?>
        <br>
        > <?php print substr($comment['body'], 0, 18); ?>

        <img class="avatar-small" src="<?php print $comment ? $comment['picture'] : $post['picture']; ?>" alt="user avatar" />

        <span class="time-ago">
          <?php print $comment ? time_ago($comment['created']) : time_ago($post['created']); ?>
        </span>
      </a>
    </li>
  <?php endforeach; ?>

  </ul>
  </div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme dashboard html.
function getDashboard($user) {
  if ($user) {
    $posts = getMyPosts($user);
  }
  else {
    print "Invalid user dashboard";
    exit;
  }

  ob_start(); ?>
  <?php if ($user): ?>
    <?php foreach ($posts as $post): ?>
      <p>
        <?php print getUser($post['uid'])->name; ?>
        posted
        <a href="/conversations/post.php?id=<?php print $post['id']; ?>">
          <?php if (empty($post['body'])): ?>
            (untitled)
          <?php else: ?>
            <?php print substr($post['body'], 0, 128); ?>
          <?php endif; ?>
        </a>
      </p>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme new post form html.
function getNewPostForm($user) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);

  ob_start(); ?>
  <h1>Start a New Topic</h1>

  <form action="submitpost.php" method="POST">
    <input type="text" name="body" placeholder="Post topic (title or short message)" />
    <br>
    <input type="text" name="recipient" placeholder="Recipient email address" />
    <br>
    <input type="text" name="link" placeholder="Link URL (optional)" />
    <br>
    <span id="user-message" /><?php print $message; ?></span>
    <input type="submit" id="submit-button" value="Post Topic" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme new comment form.
function getPostCommentForm($user, $post) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);

  ob_start(); ?>

  <form action="submitcomment.php" method="POST" id="comment-form">
    <span id="user-message" /><?php print $message; ?></span>
    <input type="hidden" name="parent_id" value="<?php print $post['id']; ?>" />
    <textarea name="body" id="comment-body" rows="1"></textarea>
    <br>
    <br>
    <input type="submit" id="submit-button" value="Send" />
    <input type="hidden" name="link" id="comment-link" placeholder="Link (optional)"/>
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme post html.
function viewPost($post) {
  global $user;

  $title = nl2br($post['body']);
  if (empty($post['link']) && empty($post['body'])) {
    $body = '(untitled)';
  }
  else if (!empty($post['link']) && !empty($post['body'])) {
    $body = '<a target="_blank" href="' . $post['link'] . '">' . $title . '</a>';
  }
  else if (empty($post['link'])) {
    $body = $title;
  }
  else {
    $body = '<a target="_blank" href="' . $post['link'] . '">' . $post['link'] . '</a>';
  }

  // Listing users with access to this post.
  $users = [];
  $uids = getPostAccess($post['id']);
  foreach ($uids as $uid) {
//    if ($uid !== $user->id) {
      $users[] = getUser($uid);
//    }
  }

  ob_start(); ?>
  <h1 id="contentheader"><?php print $body; ?></h1>
  <span class="time-ago">
    posted <?php print time_ago($post['created']); ?> by
    <span class="user-access">
      <img src="<?php print $post['picture']; ?>" alt="user avatar" />
      <?php print $post['name']; ?>
    </span>
    <?php if ($users[0]->id && $users[0]->id !== $post['uid']): ?>
      and shared with
      <?php foreach ($users as $u): ?>
        <?php if ($u->id != $post['uid']): ?>
         <span class="user-access">
          <img src="<?php print $u->picture; ?>" alt="user avatar" />
          <?php print $u->name; ?>
          </span>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
    <br>
    <span class="user-access">
      <a href="#">delete this topic</a>,
    </span>
    <span class="user-access">
      <a href="#" title="New users must login first">sharing</a>
    </span>
  </span>
  <div class="post-body"></div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme search html.
function getSearchForm($q = '') {

  ob_start(); ?>

  <form id="search-form" action="/conversations/search.php" method="get">
    <input type="text" name="q" placeholder="Search" value="<?php print $q; ?>" id="q" />
    <input type="submit" value="Go" class="submit-button" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}
function getHtmlFooter() {



}
