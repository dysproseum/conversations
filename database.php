<?php

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

// Helper function to get user info.
function getUserInfo($sub) {
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

// Helper function to return posts created and shared with me.
function getMyPosts($user) {
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
  $stmt = $mysqli->prepare("SELECT * FROM posts p, users u WHERE p.parent_id = ? AND u.id = p.uid ORDER BY created ASC");
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

function getLastComment($id) {
  global $mysqli;
  $stmt = $mysqli->prepare("SELECT * FROM posts p, users u WHERE p.parent_id = ? AND u.id = p.uid ORDER BY created DESC LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  foreach ($result as $row) {
    $post = $row;
  }
  return $post;
}

//mysqli_close($link);
