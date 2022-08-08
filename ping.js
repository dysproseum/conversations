/**
 * Javascript for comment ping functionality.
 */

function loadDoc(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var data = this.responseText;
        if (data > commentId) {
          console.log("Newer comments available");
          // Display a message in #chat.
          var msg = document.getElementById('user-message');
          msg.innerHTML = '<a href="&refresh">Newer comments available</a>';
        }
      }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
    return true;
}

var timeout;
var timeOut;
var delay = 15000;
//var postId = 0;
//var commentId = 0;
var url = "/conversations/ping.php?id=" + postId;

timeOut = function() {
  if (loadDoc(url + "&comment=" + commentId)) {
    delay = 5000;
    setTimeout(timeOut,
      delay);
  }
}
