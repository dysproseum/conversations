/**
 * Javascript used for Google signin and backend integration.
 */

// Callback function for Google account signin.
function onSignIn(googleUser) {
  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.

  var $message = document.getElementById('user-message');
  var $name = document.getElementById('user-name');
  var $picture = document.getElementById('user-picture');
  var $continue = document.getElementById('user-continue');

  // Login to the backend.
  var id_token = googleUser.getAuthResponse().id_token;
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '/conversations/tokensignin.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
	    console.log(xhr.responseText);
    var info = JSON.parse(xhr.responseText);
    if (!info.user) {
      $message.textContent = "Error: " + info.message;
    }
    else {
      // Login success.
      $name.textContent = info.user.name;
      //$picture.src = info.user.picture;
      $picture.src = profile.getImageUrl();
      $continue.hidden = false;
      $message.textContent = '';

      console.log("info.message: " + info.message);
      console.log('Signed in as: ' + info.user.email);
    } 
  };
  xhr.send('idtoken=' + id_token + "&picture=" + profile.getImageUrl());
  $picture.src = "images/loading.gif";
  $picture.hidden = false;
}

// Callback function for Google account signout.
function signOut() {
  var auth2 = gapi.auth2.getAuthInstance();
  auth2.signOut().then(function () {
    console.log('Google user signed out.');
  });

  var $message = document.getElementById('user-message');
  var $name = document.getElementById('user-name');
  var $picture = document.getElementById('user-picture');
  var $continue = document.getElementById('user-continue');

  window.location.href="/conversations";

  // Logout from the backend.
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '/conversations/tokensignout.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    console.log('Backend user signed out.');
    $message.textContent = '';
    $name.textContent = '';
    $picture.src = '';
    $picture.hidden = true;
    $continue.hidden = true;
  };
  xhr.send();
}
