/**
 * Javascript for comment ping functionality.
 */

function loadDoc(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == XMLHttpRequest.DONE) {
        if (this.status == 200) {
          var data = JSON.parse(this.responseText);

          // Update latest comment id.
          commentId = parseInt(commentId);
          if (data.latest_id > commentId) {
            commentId = data.latest_id;
            retryCount = 0;

            // Response now contains an array of comments.
            data.comments.forEach(function(comment, index) {

              // Display message(s) in #chat.
              if (comment.parent_id == postId) {
                var chat = document.getElementById("chat");
                var next = document.createElement("div");
                next.innerHTML = comment.html;
                chat.appendChild(next);
                chat.scrollTop = chat.scrollHeight;
              }

            });

            // Issue browser notification(s).
            data.notifications.forEach(function(comment, index) {
              console.log("notifying " + comment.id);

              if (getNotificationPermission()) {
                manageAccount(null, function(prefs) {
                  if (prefs.notify_banner > 0) {
                    notifyMe(comment);
                  }
                });
              }
            });

            // Play sound once.
            if (data.notifications.length > 0) {
              manageAccount(null, function(prefs) {
                if (prefs.notify_sound > 0) {
                  notifyaudio.play();
                }
              });
            }
          }
        }
        exponentialBackoff();
      }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
    return true;
}

var timeOut;
const initialDelay = 5 * 1000;
const maxDelay = 5 * 60 * 1000;
var delay = initialDelay;
var retryCount = 0;
var url = "/conversations/ping.php";


if (!"commentId" in window) {
  var commentId = '';
}

timeOut = function() {
  console.log({commentId});
  if (typeof postId === 'undefined') {
    loadDoc(url + "?comment=" + commentId);
  }
  else {
    loadDoc(url + "?id=" + postId + "&comment=" + commentId);
  }
}

exponentialBackoff = function() {
  delay = initialDelay + (initialDelay * Math.pow(2.0, retryCount));
  retryCount++;
  if (delay > maxDelay) delay = maxDelay;
  setTimeout(timeOut, delay);
}

window.addEventListener("load", function() {
  setTimeout(timeOut, initialDelay);
});
