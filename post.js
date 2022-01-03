/**
 * Javascript for post page.
 */

window.onload=function () {
  // Scroll to bottom of chat window on load.
  var objDiv = document.getElementById("chat");
  objDiv.scrollTop = objDiv.scrollHeight;
  document.getElementById("comment-body").focus();

  // Set up ping.
  timeout = setTimeout(function() {
    timeOut();
  }, delay);

  // Disable form for visual indication
  // and to prevent double posts.
  var form = document.getElementById('comment-form');
  form.onsubmit = function() {
    document.getElementById('comment-body').disabled = true;
    document.getElementById('comment-link').disabled = true;
    document.getElementById('submit-button').disabled = true;
  };

  // Submit with shift+enter key combo.
  var shift = false;
  window.onkeydown = function(e){
    if (shift == true) {
      if(e.keyCode == 13){
        e.preventDefault();
        form.submit();
        form.onsubmit();
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
