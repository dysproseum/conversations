<?php

// Prevent web access.
if ($_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
  header('HTTP/1.0 403 Forbidden', TRUE, 403);
  exit("Forbidden");
}

// Connect to the database.
require_once('config.php');
global $conf;
$db = $conf['database'];

global $mysqli;
$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
if($mysqli->connect_error) {
  exit('Error connecting to database'); //Should be a message a typical user could understand in production
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli->set_charset("utf8mb4");

// Create users table.
try {
  $test = $mysqli->query("SELECT id FROM users");
} catch (mysqli_sql_exception $e) {
  $query = "CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  sub varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  picture varchar(255) NOT NULL,
  given_name varchar(255),
  family_name varchar(255),
  locale varchar(255) NOT NULL,
  primary key(id)
  )";
  $mysqli->query($query);
}

// Create posts table.
try {
  $test = $mysqli->query("SELECT id FROM posts");
} catch (mysqli_sql_exception $e) {
  $query = "CREATE TABLE posts (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_id int(11),
  uid int(11) NOT NULL,
  created int(11) NOT NULL,
  link text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  body text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  primary key(id)
  )";
  $mysqli->query($query);
}

// Create access table.
try {
  $test = $mysqli->query("SELECT id FROM access");
} catch (mysqli_sql_exception $e) {
  $query = "CREATE TABLE access (
  id int(11),
  uid int(11)
  )";
  $mysqli->query($query);
}

// Get a user object by ID.
function getUser($id) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT * FROM users WHERE id LIKE ? LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if ($result) {
    while ($row = $result->fetch_object()){
      return $row;
    }
  }
  return FALSE;
}

// Helper function to get user info.
function getUserInfo($sub) {
  if (!$sub) {
    return FALSE;
  }
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT * FROM users WHERE sub LIKE ? LIMIT 1");
  $stmt->bind_param('s', $sub);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if ($result) {
    while ($row = $result->fetch_object()){
      return $row;
    }
  }
  return FALSE;
}

// Function to get corresponding users.
function getBuddies($user) {
  // SELECT * FROM access a WHERE 
}

// Helper function to create a new user in the database.
function newUser($result) {
  global $mysqli;
  $stmt = $mysqli->prepare("INSERT INTO users (email, sub, name, picture, given_name, family_name, locale) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param('sssssss',
    $result->email,
    $result->sub,
    $result->name,
    $result->picture,
    $result->given_name,
    $result->family_name,
    $result->locale
  );
  $stmt->execute();
  $stmt->close();
}

// Update picture.
function updatePicture($userid, $picture) {
  global $mysqli;
  $stmt = $mysqli->prepare("UPDATE users SET picture = ? WHERE sub=?");
  $stmt->bind_param('ss',
    $picture,
    $userid,
  );
  $stmt->execute();
  $stmt->close();
}

// Get user id for recipient.
function getUserIDByEmail($recipient) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT id FROM users WHERE email like ? LIMIT 1");
  $stmt->bind_param('s', $recipient);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  foreach ($result as $row) {
    $uid = $row['id'];
  }

  return $uid;
}

// Create access record.
function createAccess($post_id, $user_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("INSERT INTO access (id, uid) VALUES (?, ?)");
  $stmt->bind_param('ii', $post_id, $user_id);
  $stmt->execute();
  $result = $mysqli->affected_rows;
  $stmt->close();

  return $result;
}

// Helper function to return posts created and shared with a user.
function getMyPosts($user) {
  global $mysqli;
  if (!$user->id) {
    return false;
  }
  $stmt = $mysqli->prepare("SELECT p.*, u.picture FROM posts p, users u, access a WHERE a.uid = ? AND p.id = a.id AND u.id = p.uid ORDER BY created DESC");
  $stmt->bind_param('i', $user->id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $posts = [];
  foreach ($result as $row) {
    $posts[] = $row;
  }
  return $posts;
}

// Helper function to return posts created by a user.
function getPostsCreatedByUser($user) {
  global $mysqli;
  if (!$user->id) {
    return false;
  }
  $stmt = $mysqli->prepare("SELECT p.* FROM posts p WHERE p.uid = ? AND p.parent_id IS NULL ORDER BY created DESC");
  $stmt->bind_param('i', $user->id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $posts = [];
  foreach ($result as $row) {
    $posts[] = $row;
  }
  return $posts;
}

// Helper function to return posts for the dashboard.
function getAllPosts($user) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT p.* FROM posts p, access a WHERE a.uid = ? AND p.id = a.id ORDER BY created DESC");
  $stmt->bind_param('i', $user->id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $posts = [];
  foreach ($result as $row) {
    $posts[] = $row;
  }
  return $posts;
}

// Get user id's with access to post.
function getPostAccess($id) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT uid FROM access a WHERE a.id = ?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $uids = [];
  foreach ($result as $index => $row) {
    $uids[] = $row['uid'];
  }
  return $uids;
}

// Helper function to get a post from its id.
function getPost($id) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT p.*, u.name, u.picture FROM posts p, users u WHERE p.id = ? AND u.id = p.uid AND p.parent_id IS NULL");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if (!$result) {
    return NULL;
  }
  foreach ($result as $row) {
    return $row;
  }
}

// Helper function to get a post's comments.
function getPostComments($id) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT p.id AS post_id, p.*, u.* FROM posts p, users u WHERE p.parent_id = ? AND u.id = p.uid ORDER BY created ASC");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $posts = [];
  foreach ($result as $row) {
    $posts[] = $row;
  }
  return $posts;
}

// Return the most recent comment for a given post.
function getLastComment($id) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT p.id AS post_id, p.*, u.name, u.picture FROM posts p, users u WHERE p.parent_id = ? AND u.id = p.uid ORDER BY created DESC LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $post = [];
  foreach ($result as $row) {
    $post = $row;
  }
  return $post;
}

// Return the comment by id.
function getComment($id) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT p.*, u.name, u.picture FROM posts p, users u WHERE p.id = ? AND u.id = p.uid LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $post = [];
  foreach ($result as $row) {
    $post = $row;
  }
  return $post;
}

// Check if user has access to post.
function checkAccess($post, $person = '') {
  global $mysqli;

  if ($person == '') {
    global $user;
    $person = $user;
  }

  $post_id = $post['parent_id'] ? $post['parent_id'] : $post['id'];
  $user_id = $person->id;

  $stmt = $mysqli->prepare("SELECT COUNT(*) AS cnt FROM access a WHERE a.id = ? AND a.uid = ?");
  $stmt->bind_param('ii',
    $post_id,
    $user_id,
  );
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  foreach ($result as $row) {
    $count = $row['cnt'];
  }
  if ($count > 0) {
    return true;
  }

  return false;

}

// Search function.
function searchPosts($q) {
  global $user;
  global $mysqli;

  $q = strtolower(trim($q));
  if (empty($q)) {
    return [];
  }
  else {
    $q = "%$q%";
  }

  $stmt = $mysqli->prepare("SELECT p.* FROM posts p WHERE p.body LIKE LOWER(?) ORDER BY p.created DESC");
  $stmt->bind_param('s', $q);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $row) {
    if (checkAccess($row)) {
      if ($row['parent_id'] != NULL) {
        $parent = getPost($row['parent_id']);
        if (!$parent) {
          continue;
        }

        $row['title'] = $parent['body'] ? $parent['body'] : '(untitled)';
        $row['created'] = $parent['created'];
        $last = getLastComment($row['parent_id']);
        $row['updated'] = $last['created'];
        $row['cid'] = $row['id'];
        $row['id'] = $parent['id'];
      }
      else {
        $row['title'] = $row['body'];
        $row['body'] = '';
        $last = getLastComment($row['id']);
        if (!$last) {
          $row['updated'] = $row['created'];
        }
        else {
          $row['updated'] = $last['created'];
        }
        $row['cid'] = $last['id'];
      }
      $posts[] = $row;

    }
  }
  return $posts;
}

// Generate report on posts and stray data.
function generateReport() {
  global $mysqli;

  $report = array(
    'num_posts' => 0,
    'num_comments' => 0,
    'num_users' => 0,
    'posts_no_access' => [],
    'access_no_posts' => [],
    'comment_no_posts' => [],
  );

  $stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM posts p WHERE p.parent_id IS NULL");
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $r) {
    $report['num_posts'] = $r['cnt'];
  }

  $stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM posts p WHERE p.parent_id IS NOT NULL");
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $r) {
    $report['num_comments'] = $r['cnt'];
  }

  $stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM users u");
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $r) {
    $report['num_users'] = $r['cnt'];
  }

  // Posts with no access.
  $stmt = $mysqli->prepare("SELECT p.id, p.body FROM posts p WHERE p.parent_id IS NULL ORDER BY p.id DESC");
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $r) {
    $rid = $r['id'];

    $stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM access WHERE id = ?");
    $stmt->bind_param('i', $rid);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();

    foreach ($result2 as $r2) {
      if ($r2['cnt'] == 0) {
        $report['posts_no_access'][$rid] = "post $rid: " . $r['body'];
        $report['posts_no_access'][$rid] .= ": " . $r2['cnt'];
      }
    }
  }

  // Access with no posts.
  $stmt = $mysqli->prepare("SELECT a.id, a.uid FROM access a ORDER BY a.id DESC");
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $r) {
    $rid = $r['id'];

    $stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM posts WHERE id = ?");
    $stmt->bind_param('i', $rid);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();

    foreach ($result2 as $r2) {
      if ($r2['cnt'] == 0) {
        $report['access_no_posts'][$rid] = "post $rid: uid " . $r['uid'];
        $report['access_no_posts'][$rid] .= ": " . $r2['cnt'];
      }
    }
  }

  // Stray comments.
  $stmt = $mysqli->prepare("SELECT p.id, p.parent_id FROM posts p WHERE p.parent_id IS NOT NULL ORDER BY p.id DESC");
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $r) {
    $rid = $r['parent_id'];

    $stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM posts WHERE id = ?");
    $stmt->bind_param('i', $rid);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();

    foreach ($result2 as $r2) {
      if ($r2['cnt'] == 0) {
        $report['comment_no_posts'][$rid] = "post $rid: comment: " . $r['id'];
        $report['comment_no_posts'][$rid] .= ": " . $r2['cnt'];
      }
    }
  }

  return $report;
}

// Ping function to get updates.
function getPing($post_id, $comment_id = '') {

  // @todo get unread posts.
  // @todo implement unread status.

  $last = getLastComment($post_id);
  $response = [
    'comment' => $last,
  ];

  // @todo implement caching bucket.
  // @todo expire cache upon post creation, for each user that has access.

  return $response;
}

// @todo nothing calls this yet
function dayByDatabase($user) {
  global $mysqli;
  if (!$user->id) {
    return false;
  }
  $today_order_by_date_desc;
  $posts = getMyPosts($user);

  $stmt = $mysqli->prepare("SELECT p.* FROM posts p, access a WHERE a.uid = ? AND p.id = a.id ORDER BY created DESC");
  $stmt->bind_param('i', $user->id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  $posts = [];
  foreach ($result as $row) {
    $posts[] = $row;
  }
  return $posts;
}

// Remove user access to post.
function removeAccess($post_id, $user_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM access WHERE id = ? and uid = ? LIMIT 1");
  $stmt->bind_param('ii', 
    $post_id,
    $user_id,
  );
  $stmt->execute();
  $result = $mysqli->affected_rows;
  $stmt->close();

  return $result;
}

// Delete post.
function deletePost($post_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ? LIMIT 1");
  $stmt->bind_param('i', $post_id);
  $stmt->execute();
  $result = $mysqli->affected_rows;
  $stmt->close();

  return $result;
}

// Delete comments.
function deletePostComments($post_id) {
  global $mysqli;

  $num_comments = getPostComments($post_id);

  $stmt = $mysqli->prepare("DELETE FROM posts WHERE parent_id = ?");
  $stmt->bind_param('i', $post_id);
  $stmt->execute();
  $result = $mysqli->affected_rows;
  $stmt->close();

  if ($result == sizeof($num_comments)) {
    return true;
  }
  else {
    return false;
  }
}
