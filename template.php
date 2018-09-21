<?php

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

function getSidebar($user) {
  ob_start(); ?>
  <?php if ($user): ?>
    <a href="/conversations/new.php">New post</a>
  <?php endif; ?>
  <ul>
    <li>Recent posts</li>
    <li>Other</li>
  </ul>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

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

function getNewPostForm($user) {
  ob_start(); ?>
  <h1>New Post</h1>
  <form action="submitpost.php" method="POST">
    <textarea name="body"></textarea>
    <br>
    <input type="text" name="link" />
    <br>
    <input type="submit" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

function getPostCommentForm($user, $post) {
  ob_start(); ?>
  <form action="submitcomment.php" method="POST" id="comment-form">
    <input type="hidden" name="parent_id" value="<?php print $post['id']; ?>" />
    <textarea name="body" id="comment-body"></textarea>
    <br>
    <input type="text" name="link" id="comment-link"/> 
    <br>
    <input type="submit" id="submit-comment" />
  </form>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

function viewPost($post) {
  ob_start(); ?>
  <h1>View Post</h1>
  <p>
    <img class="avatar-small-left" src="<?php print $post['picture']; ?> alt="user avatar" />
    <?php print $post['name']; ?>
    <?php print date('Y-m-d h:i a', $post['created']); ?>
  </p>
  <p><?php print $post['body']; ?></p>
  <p><a href="<?php print $post['link']; ?>"><?php print $post['link']; ?></a></p>

  <?php $html = ob_get_contents();
  ob_end_clean();
  return $html;
}
