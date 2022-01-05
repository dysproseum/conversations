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
    document.getElementById('submit-button').classList.remove('active');
    document.getElementById('comment-body').disabled = true;
    document.getElementById('comment-link').disabled = true;
    document.getElementById('submit-button').disabled = true;
  };

  // Submit with ctrl+enter key combo.
  var modifier = false;
  var ctrl = 17;
  var enter = 13;
  form.onkeydown = function(e){
      if(e.keyCode == enter){
console.log('enter');
        if (modifier == true) {
          e.preventDefault();
          form.submit();
          form.onsubmit();
        }
        else {
          document.getElementById('comment-body').rows = "5";
        }
    }
    else if (e.keyCode == ctrl) {
      document.getElementById('submit-button').classList.add('active');
      modifier = true;
    }
  };
  form.onkeyup = function(e){
    if (e.keyCode == ctrl) {
      modifier = false;
      document.getElementById('submit-button').classList.remove('active');
    }
  };

}
