/**
 * Javascript used for Google signin and backend integration.
 */

// Callback function for Google account signin.
function handleCredentialResponse(response) {
  // decodeJwtResponse() is a custom function defined by you
  // to decode the credential response.
  const responsePayload = decodeJwtResponse(response.credential);

  console.log("ID: " + responsePayload.sub);
  console.log('Full Name: ' + responsePayload.name);
  console.log('Given Name: ' + responsePayload.given_name);
  console.log('Family Name: ' + responsePayload.family_name);
  console.log("Image URL: " + responsePayload.picture);
  console.log("Email: " + responsePayload.email);

  var $message = document.getElementById('user-message');
  var $name = document.getElementById('user-name');
  var $picture = document.getElementById('user-picture');
  var $continue = document.getElementById('user-continue');

  // Login to the backend.
  var id_token = response.credential;
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '/conversations/tokensignin.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    var info = JSON.parse(xhr.responseText);
    if (!info.user) {
      $message.textContent = "Error: " + info.message;
    }
    else {
      // Login success.
      if ($name) {
        $name.textContent = info.user.name;
      }
      if ($picture) {
        $picture.src = info.user.picture
      }
      if ($continue) {
        $continue.hidden = false;
      }
      if ($message) {
        $message.textContent = '';
      }

      console.log("info.message: " + info.message);
      console.log('Signed in as: ' + info.user.email);

      window.location.href="/conversations/dashboard.php";
    }
  };
  xhr.send('idtoken=' + id_token + "&picture=" + responsePayload.picture);

  if ($picture) {
    $picture.src = "images/loading.gif";
    $picture.hidden = false;
  }
}

// Callback function for Google account signout.
function signOut() {

  var $message = document.getElementById('user-message');
  var $name = document.getElementById('user-name');
  var $picture = document.getElementById('user-picture');
  var $continue = document.getElementById('user-continue');

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

    window.location.href="/conversations/index.php";
  };
  xhr.send();

}

// Decode JWT token.
function decodeJwtResponse(token) {
  var base64Url = token.split('.')[1];
  var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));

  return JSON.parse(jsonPayload);
}
