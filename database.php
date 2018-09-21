<?php

// Connect to the database.
require_once('config.php');
global $conf;
$db = $conf['database'];
global $link;
$link = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);

if (!$link) {
  echo "Error: Unable to connect to MySQL." . PHP_EOL;
  echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
  echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
  exit;
}

// Create users table.
$query = "SELECT id FROM users";
$test = mysqli_query($link, $query);

if(empty($test)) {
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
  mysqli_query($link, $query);
}

// Create posts table.
$query = "SELECT id FROM posts";
$test = mysqli_query($link, $query);

if (empty($test)) {
  $query = "CREATE TABLE posts (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_id int(11),
  uid int(11) NOT NULL,
  created int(11) NOT NULL,
  link text,
  body text,
  primary key(id)
  )";
  mysqli_query($link, $query);
}

// Create access table.
$query = "SELECT id FROM access";
$test = mysqli_query($link, $query);

if (empty($test)) {
  $query = "CREATE TABLE access (
  id int(11),
  uid int(11)
  )";
  mysqli_query($link, $query);
}

// Helper function to get user info.
function getUserInfo($sub) {
  global $link;
  $query = "SELECT * FROM users WHERE sub LIKE '" . $sub . "' LIMIT 1";
  $result = mysqli_query($link, $query);
  if ($result) {
    while ($row = $result->fetch_object()){
      return $row;
    }
  }
  return FALSE;
}

// Helper function to create a new user in the database.
function newUser($result) {
  global $link;
  $query = "INSERT INTO users (email, sub, name, picture, given_name, family_name, locale) VALUES (
    '" . $result->email . "',
    '" . $result->sub . "',
    '" . $result->name . "',
    '" . $result->picture . "',
    '" . $result->given_name . "',
    '" . $result->family_name . "',
    '" . $result->locale . "')";
  print $query;
  $result = mysqli_query($link, $query);
}

// Helper function to return posts created and shared with me.
function getMyPosts($user) {
  global $link;
  $query = "SELECT p.* FROM posts p, access a WHERE a.uid = " . $user->id . " AND p.id = a.id ORDER BY created DESC";
  $result = mysqli_query($link, $query);
  $posts = [];
  foreach ($result as $row) {
    $posts[] = $row;
  }
  return $posts;
}

// Helper function to get a post from its id.
function getPost($id) {
  global $link;
  $query = "SELECT p.*, u.name, u.picture FROM posts p, users u WHERE p.id = $id AND u.id = p.uid AND p.parent_id IS NULL";
  $result = mysqli_query($link, $query);
  if (!$result) {
    return NULL;
  }
  foreach ($result as $row) {
    return $row;
  }
}

// Helper function to get a post's comments.
function getPostComments($id) {
  global $link;
  $query = "SELECT * FROM posts p, users u WHERE p.parent_id = " . $id . " AND u.id = p.uid ORDER BY created ASC";
  $result = mysqli_query($link, $query);
  $posts = [];
  foreach ($result as $row) {
    $posts[] = $row;
  }
  return $posts;
}

function getLastComment($id) {
  global $link;
  $query = "SELECT * FROM posts p, users u WHERE p.parent_id = " . $id . " AND u.id = p.uid ORDER BY created DESC LIMIT 1";
  $result = mysqli_query($link, $query);
  foreach ($result as $row) {
    $post = $row;
  }
  return $post;
}

//mysqli_close($link);
