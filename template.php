<?php

require_once('utils.php');

// Theme header html.
function getHeader($user) {
  ob_start(); ?>
  <h1><a href="/conversations">conversations</a></h1>
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
function getSidebar($user) {
  ob_start(); ?>
  <ul>
  <?php if ($user): ?>
    <li>
      <a href="/conversations/new.php">New post</a>
    </li>
  <?php foreach (getMyPosts($user) as $post_id): ?>
    <?php $post = getPost($post_id['id']); ?>
    <?php $comment = getLastComment($post_id['id']); ?>
    <li>
      <a href="/conversations/post.php?id=<?php print $post['id']; ?>">Post <?php print $post['id']; ?>
      <img class="avatar-small-left" src="<?php print $comment['picture']; ?>" alt="user avatar" />
      <!-- @todo read/unread status -->
      <!-- @todo time ago -->
      <?php print $comment ? time_ago($comment['created']) : time_ago($post['created']); ?>
      </a>
    </li>
  <?php endforeach; ?>
  <?php endif; ?>
    <li><a href="#">Search</a></li>
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
        Post <?php print $post['id']; ?>
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
    <input type="text" name="link" id="comment-link"/> 
    <br>
    <input type="submit" id="submit-comment" value="Enter" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Theme post html.
function viewPost($post) {
  ob_start(); ?>
  <p>
    <img class="avatar-small-left" src="<?php print $post['picture']; ?> alt="user avatar" />
    <strong><?php print $post['name']; ?></strong>
    <br>
    <?php print time_ago($post['created']); ?>
  </p>
  <div class="post-body">
    <?php print $post['body']; ?>
  </div>
  <p><a href="<?php print $post['link']; ?>"><?php print $post['link']; ?></a></p>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}
