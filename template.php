<?php

require_once('utils.php');

define('SITE_NAME', 'Search ฅ^•ﻌ•^ฅ');

// Theme header html.
function getHeader($user) {
  $name = $user ? $user->name : '';
  $img = $user ? $user->picture: 'transparent.gif';

  ob_start(); ?>
  <div id="header">
    <h1><a href="/conversations/search.php">conversations</a></h1>
    <div class="profile-block">
      <img id="user-picture" src="<?php print $img; ?>" />
    </div>
    <div class="profile-block">
        <span id="user-name">Hello <?php print $name; ?></span><br>
        <a href="/conversations/login.php">User Account</a>
    </div>
  </div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme sidebar html.
function getSidebar($user, $this_post = '') {

  $posts = getMyPosts($user);
  $sorted = [];
  // Re-sort posts.
  foreach ($posts as $post) {
    $comment = getLastComment($post['id']);
    $sorted[$comment['created']] = $post;
  }
  print "<br>\n";
  krsort($sorted);

  ob_start(); ?>
  <div class="sidebar">
    <ul>
    <?php if ($user): ?>
    <li class="post search <?php if ($this_post == "search") print "active"; ?>">
      <a href="/conversations/search.php">Search ></a>
    </li>
      <li class="<?php if ($this_post == "new") print "active"; ?>">
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
          <span class="preview">
            > <?php print substr($comment['body'], 0, 18); ?>
          </span>
          <br />
          <span class="time-ago">
            <?php print $comment ? time_ago($comment['created']) : time_ago($post['created']); ?>
          </span>
          <img class="avatar-small" src="<?php print $comment ? $comment['picture'] : $post['picture']; ?>" alt="user avatar" />
        </a>
      </li>
    <?php endforeach; ?>
      <li class="<?php if ($this_post == "new") print "active"; ?>">
      <img src="newfileicon.png" alt="new post" width="36" />
        <a href="/conversations/new.php">New post</a>
      </li>
    <?php endif; ?>
      <li class="<?php if ($this_post == "reports") print "active"; ?>">
        <a href="/conversations/reports.php">Reports</a>
      </li>
    </ul>
  </div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme sidebar html.
function getSidebar2($user, $this_post = '') {
  global $user;

  ob_start(); ?>
  <div class="sidebar" id="sidebar2">
  <ul>
  <li class="post">
    <div class="profile-block">
        <img id="user-picture" src="<?php print $user->picture; ?>" />
    </div>
    <div class="profile-block">
        <span id="user-name"><?php print $user->name; ?></span><br>
    </div>
        <a href="/conversations/login.php">My Account</a>
  </li>
  <li class="post">
    <div class="profile-block">
      Buddy List
    </div>
      <a href="#">Add buddy/friend/user/recipient</a>
  </li>

  <?php foreach (getMyPosts($user) as $post_id): ?>
    <?php $post = getPost($post_id['id']); ?>
    <?php $comment = getLastComment($post_id['id']); ?>
    <li class="post <?php if($post_id['id'] == $this_post) print "active"; ?>">
      <a href="/conversations/post.php?id=<?php print $post['id']; ?>"><?php print substr($comment['body'], 0, 18); ?>
      <img class="avatar-small-left" src="<?php print $comment ? $comment['picture'] : $post['picture']; ?>" alt="user avatar" />
      <!-- @todo read/unread status -->
      <!-- @todo time ago -->
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
    <input type="hidden" name="parent_id" value="<?php print $post['id']; ?>" />
    <textarea name="body" id="comment-body" rows="1"></textarea>
    <br>
    <input type="text" name="link" id="comment-link" placeholder="Link (optional)"/>
    <br>
    <span id="user-message" /><?php print $message; ?></span>
    <input type="submit" id="submit-button" value="Send" />
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
    if ($uid !== $user->id) {
      $users[] = getUser($uid);
    }
  }

  ob_start(); ?>
  <h1><?php print $body; ?></h1>
  <span class="time-ago">
    posted <?php print time_ago($post['created']); ?> by
    <span class="user-access">
      <img src="<?php print $post['picture']; ?>" alt="user avatar" />
      <?php print $post['name']; ?>
    </span>
    <?php if ($users[0]->id && $users[0]->id !== $post['uid']): ?>
      and shared with
      <?php foreach ($users as $u): ?>
        <?php if ($u->id == $post['uid']): ?>
          me
        <?php else: ?>
         <span class="user-access">
          <img src="<?php print $u->picture; ?>" alt="user avatar" />
          <?php print $u->name; ?>
          </span>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
    <span class="user-access-right">
      <a href="#">Delete this post</a>
    </span>
    <span class="user-access-right">
      <a href="#">Invite more</a>
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
