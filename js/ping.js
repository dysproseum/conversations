/**
 * Javascript for comment ping functionality.
 */

function loadDoc(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == XMLHttpRequest.DONE) {
        if (this.status == 200) {
          var data = JSON.parse(this.responseText);
          if (data.comment.id > commentId) {
            commentId = data.comment.id;
            retryCount = 0;

            // Display a message in #chat.
            var chat = document.getElementById("chat");
            var next = document.createElement("div");
            next.innerHTML = data.html;
            chat.appendChild(next);
            chat.scrollTop = chat.scrollHeight;
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
var url = "/conversations/ping.php?id=" + postId;

timeOut = function() {
  loadDoc(url + "&comment=" + commentId);
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
