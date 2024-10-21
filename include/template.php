<?php

require_once('include/utils.php');

define('SITE_NAME', 'Conversations ฅ^•ﻌ•^ฅ');

// Global items to place in html head tag.
function getHtmlHeader($options) {

  // Prevent notifications on page load.
  $last_id = getLatestCommentId();

  ob_start(); ?>
  <title><?php print $options['title'] . ' | ' . SITE_NAME; ?></title>

  <script type="text/javascript">
    var postId;
    var commentId = <?php print $last_id; ?>;
  </script>
  <script type="text/javascript" src="js/fullscreen.js"></script>
  <script type="text/javascript" src="js/ping.js"></script>
  <script type="text/javascript" src="js/post.js"></script>
  <script type="text/javascript" src="js/drag.js"></script>
  <script type="text/javascript" src="js/notify.js"></script>

  <link rel="stylesheet" type="text/css" href="css/styles.css" media="screen">
  <link rel='stylesheet' media='only screen and (max-width: 768px)' href='css/mobile.css?<?php print time(); ?>' type='text/css' />

  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0" />

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme header html.
function getHeader($user) {
return;
  $name = $user ? $user->name : '';
  $img = $user ? $user->picture: 'images/transparent.gif';

  ob_start(); ?>
  <div id="header">
      <a href="/conversations/search.php" title="Home">Conversations</a>
    <div class="profile-block">
      <a href="<?php print $conf['minimize']; ?>" id="minimize">
          <img src="images/min-button.png" alt="Minimize" title="Hide" />
        </a>
        <a href="#" onclick="toggleFullscreen(this)" id="maximize">
          <img src="images/max-button.png" alt="Maximize" title="Fullscreen" style="margin-right: 10px" />
        </a>
        <a href="/conversations/login.php" id="exit">
          <img src="images/x-icon.png" alt="Exit" title="Logout" />
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

  ob_start(); ?>
  <div class="sidebar" id="sidebar1">
    <ul>
    <?php if ($user): ?>
      <li class="post new <?php if ($this_post == "dashboard") print "active"; ?>">
        <a href="/conversations/dashboard.php">Dashboard</a>
      </li>
      <li class="post new <?php if ($this_post == "search") print "active"; ?>">
        <a href="/conversations/search.php">Search &#x1F50E;</a>
      </li>
      <li class="post new <?php if ($this_post == "new") print "active"; ?>">
        <a href="/conversations/new.php">New Topic +</a>
      </li>
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
  global $conf;
  global $user;

  $posts = getMyPosts($user);
  $sorted = [];
  if ($posts) {
    // Re-sort posts by latest comment.
    foreach ($posts as $post) {
      $comment = getLastComment($post['id']);
      if (isset($comment['created'])) {
        $sorted[$comment['created']] = $post;
      }
      else {
        $sorted[$post['created']] = $post;
      }
    }
    krsort($sorted);
  }

  if (isset($user->name)) {
    $username = $user->name;
  }
  else {
    $username = 'Unknown';
  }
  if (isset($user->picture)) {
    $picture = $user->picture;
  }
  else {
    $picture = 'images/unknown-user.jpg';
  }

  ob_start(); ?>
  <div class="sidebar" id="sidebar2">
  <ul>
  <li class="active header">
    <a href="#" style="float: left">Conversations</a>

        <a href="<?php print $conf['minimize']; ?>" id="minimize">
          <img src="images/min-button.png" alt="Minimize" title="Hide" />
        </a>
        <a href="#" onclick="toggleFullscreen(this)" id="maximize">
          <img src="images/max-button.png" alt="Maximize" title="Fullscreen" style="margin-right: 10px" />
        </a>
        <a href="/conversations/login.php" id="exit">
          <img src="images/x-icon.png" alt="Exit" title="Logout" />
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

  <?php foreach ($sorted as $post): ?>
    <?php $comment = getLastComment($post['id']); ?>
    <?php $time_ago = $comment ? time_ago($comment['created']) : time_ago($post['created']); ?>
    <?php $picture = $comment ? $comment['picture'] : $post['picture']; ?>
    <?php $link = "/conversations/post.php?id=" . $post['id'] . "&cid=" . $comment['id']; ?>

    <li class="post <?php if($post['id'] == $this_post) print "active"; ?>">
      <a href="<?php print $link; ?>" title="<?php print $time_ago; ?>">

        <?php if (!empty($post['body'])): ?>
          <?php print substr($post['body'], 0, 24); ?>
        <?php elseif (!empty($post['link'])): ?>
          <?php print substr($post['link'], 0, 18); ?>
        <?php else: ?>
          (untitled)
        <?php endif; ?>
        <br>
        > <?php print substr($comment['body'], 0, 18); ?>

        <img class="avatar-small" src="<?php print $picture; ?>" alt="user avatar" />

        <span class="time-ago"></span>
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
  <?php if (sizeof($posts) == "0"): ?>
    <div id="content">
      <h1 id="contentheader">Dashboard</h1>
      <p>No posts yet! Click here to create one:</p>
      <p>
        <a id="submit-button" href="/new.php">New Post</a>
      </p>
    </div>
  <?php endif; ?>
  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme search html.
function getSearchDefault($user) {
  if ($user) {
    $posts = getMyPosts($user);
  }
  else {
    print "Invalid user search";
    exit;
  }

  ob_start(); ?>
    <?php foreach ($posts as $post): ?>
      <li>
        <a href="/conversations/post.php?id=<?php print $post['id']; ?>">
          <?php if (empty($post['body'])): ?>
            (untitled)
          <?php else: ?>
            <?php print substr($post['body'], 0, 128); ?>
          <?php endif; ?>
        </a>
        <br>
        <?php $comment = getLastComment($post['id']); ?>
        <?php $comments = getPostcomments($post['id']); ?>
        <?php print sizeof($comments); ?> posts
        <br>
        posted by <?php print getUser($post['uid'])->name; ?>
      </li>
    <?php endforeach; ?>
  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Print session message.
function sessionMessage() {
  if (isset($_SESSION['message'])) {
    ob_start(); ?>
<p id="user-message">
    <span>
      <?php print $_SESSION['message']; ?>
    </span>
</p>
    <?php $html = ob_get_contents();
    ob_end_clean();
    unset($_SESSION['message']);
    return $html;
  }
}

// @todo nothing calls this yet
function dayByDay($user) {
  $posts = dayByDatabase($user);

  ob_start(); ?>
  <h1>Day By Day</h1>
  <?php foreach ($posts as $post): ?>
    <?php if (isset($post)): ?>
      <?php echo $post['title']; ?>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme new post form html.
function getNewPostForm($user) {
  ob_start(); ?>
  <h1 id="contentheader">Start a New Topic</h1>

  <form action="submitpost.php" method="POST">
    <input type="text" name="body" placeholder="Post topic (title or short message)" />
    <br>
    <input type="text" name="recipient" placeholder="Recipient email address" />
    <br>
    <input type="text" name="link" placeholder="Link URL (optional)" />
    <br>
    <?php print sessionMessage(); ?>
    <input type="submit" id="submit-button" value="Post Topic" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme new comment form.
function getPostCommentForm($user, $post) {
  ob_start(); ?>
  <form action="submitcomment.php" method="POST" id="comment-form">
    <?php print sessionMessage(); ?>
    <input type="hidden" name="parent_id" value="<?php print $post['id']; ?>" />
    <div class="for-padding">
    <textarea name="body" id="comment-body" rows="1"></textarea>
    </div>
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
    <?php if (sizeof($users) > 1): ?>
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
      <a href="/conversations/access.php?id=<?php print $post['id']; ?>">access</a>
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

// Theme comment.
function buildComment($comment, &$current_img, &$current_day) {
  global $user;

  $imgs = getImagesLinks($comment['body']);
  $body = displayTextWithLinks(nl2br($comment['body']));
  $timestamp = $comment['name'] . date(' Y-m-d H:i', $comment['created']) . " UTC";
  $cid = $comment['post_id'];
  $permalink = '?id=' . $comment['parent_id'] . '&cid=' . $cid;

  ob_start(); ?>

  <div class="comment-wrapper current" id="<?php print $cid; ?>">
    <div class="comment <?php if ($comment['uid'] == $user->id) print "me"; ?>">
    <?php if (($current_img !== $comment['picture']) || ($current_day !== date('d', $comment['created']))): ?>

      <?php
        $current_img = $comment['picture'];
        $current_day = date('d', $comment['created']);
      ?>
      <img class="avatar-small current" src="<?php print $current_img; ?>" alt="user avatar" title="<?php print $timestamp; ?>" align="left" />

    <?php else: ?>

      <img class="avatar-small" src="images/transparent.gif" align="left" title="<?php print $timestamp; ?>" />

    <?php endif; ?>

    <a class="permalink" title="<?php print $timestamp; ?>" href="<?php print $permalink; ?>">Permalink</a>

    <?php if ($comment['body']): ?>
       <p><?php print $body; ?></p>
    <?php endif; ?>

    <?php if ($imgs): ?>
      <?php foreach ($imgs[0] as $img): ?>
        <p><a target="_blank" href="<?php print $img; ?>">
          <img class="comment-preview-thumb" src="<?php print $img; ?>" />
        </a></p>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($comment['link']): ?>
      <p><a target="_blank" href="<?php print $comment['link']; ?>"><?php print $comment['link']; ?></a></p>
    <?php endif; ?>

    </div>
  </div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

function getHtmlFooter() {
  ob_start(); ?>

    <audio src="include/im.wav" id="notifyaudio" />

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}
