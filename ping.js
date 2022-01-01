/**
 * Javascript for post page.
 */

function loadDoc(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
      }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
    return true;
}

var timeout;
var timeOut;
var delay = 15000;

timeOut = function() {
  if (loadDoc('ping.php')) {
    delay = 5000;
    setTimeout(timeOut,
      delay);
  }
}

window.onload=function () {

  timeout = setTimeout(function() {
    timeOut();
  }, delay);

}
