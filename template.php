<?php

require_once('utils.php');

// Theme header html.
function getHeader($user) {
  ob_start(); ?>
  <h1><a href="/conversations/dashboard.php">conversations</a></h1>
  <div class="profile-block">
    <img id="user-picture" src="<?php print $user->picture; ?>" />
  </div>
  <div class="profile-block">
    <span id="user-name">Hello <?php print $user->name; ?></span><br>
    <a href="/conversations/login.php">User Account</a>
  </div>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme sidebar html.
function getSidebar($user, $this_post = '') {
  ob_start(); ?>
  <ul>
  <?php if ($user): ?>
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
    <li>
      <a href="/conversations/new.php">New post</a>
    </li>
  <?php endif; ?>
    <li><a href="/conversations/search.php">Search</a></li>
    <li><a href="#">Reports</a></li>
  </ul>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme dashboard html.
function getDashboard($user) {
  if ($user) {
    $posts = getMyPosts($user);
  }
  ob_start(); ?>
  <h1>Dashboard</h1>
  <?php if ($user): ?>
    <?php foreach ($posts as $post): ?>
      <a href="/conversations/post.php?id=<?php print $post['id']; ?>">
        <?php print substr($post['body'], 0, 128); ?>
      </a>
      <br>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme new post form html.
function getNewPostForm($user) {
  ob_start(); ?>
  <h1>New Post</h1>
  <form action="submitpost.php" method="POST">
    <input type="text" name="recipient" placeholder="Recipient" />
    <br>
    <textarea name="body"></textarea>
    <br>
    <input type="text" name="link" placeholder="Link" />
    <br>
    <input type="submit" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme new comment form.
function getPostCommentForm($user, $post) {
  ob_start(); ?>
  <form action="submitcomment.php" method="POST" id="comment-form">
    <input type="hidden" name="parent_id" value="<?php print $post['id']; ?>" />
    <textarea name="body" id="comment-body"></textarea>
    <br>
    <input type="text" name="link" id="comment-link" placeholder="Link"/>
    <br>
    <input type="submit" id="submit-comment" value="Send" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme post html.
function viewPost($post) {
  ob_start(); ?>
  <p>
    <img class="avatar-small-left" src="<?php print $post['picture']; ?>" alt="user avatar" />
    <strong><?php print $post['name']; ?></strong>
    <br>
    <?php print time_ago($post['created']); ?>
  </p>
  <div class="post-body">
    <p>
    <?php print nl2br($post['body']); ?>
    </p>
  </div>
  <p><a href="<?php print $post['link']; ?>"><?php print $post['link']; ?></a></p>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme search html.
function getSearchForm() {

  ob_start(); ?>

  <h1>Search</h1>
  <form action="/conversations/search.php" method="get">
    <input type="text" name="q" placeholder="Search" />
    <input type="submit" value="Search" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}
