/**
 * Javascript for post page.
 */

window.onload=function () {
  // Scroll to bottom of chat window on load.
  var objDiv = document.getElementById("chat");
  objDiv.scrollTop = objDiv.scrollHeight;

  // Submit with shift+enter key combo.
  var form = document.getElementById('comment-form');
  var shift = false;
  window.onkeydown = function(e){
    if (shift == true) {
      if(e.keyCode == 13){
        e.preventDefault();
        form.submit();
      }
    }
    else if (e.keyCode == 16) {
      shift = true;
    }
  };
  window.onkeyup = function(e){
    if (e.keyCode == 16) {
      shift = false;
    }
  };
}
