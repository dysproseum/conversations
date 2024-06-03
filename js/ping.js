/**
 * Javascript for comment ping functionality.
 */

function loadDoc(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == XMLHttpRequest.DONE) {
        if (this.status == 200) {
          var data = JSON.parse(this.responseText);
          if (data.id > commentId) {
            commentId = data.id;

            // Display a message in #chat.
            var chat = document.getElementById("chat");
            var next = document.createElement("div");
            next.innerHTML = data.html;
            chat.appendChild(next);
            //window.scrollTo(chat);
            chat.scrollTop = chat.scrollHeight;
          }
        }
      }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
    return true;
}

var timeOut;
var delay = 15000;
var url = "/conversations/ping.php?id=" + postId;

timeOut = function() {
  if (loadDoc(url + "&comment=" + commentId)) {
    delay = 5000;
    setTimeout(timeOut,
      delay);
  }
}

window.addEventListener("load", function() {
  setTimeout(timeOut, delay);
});
