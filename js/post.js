/**
 * Javascript for post page.
 */

window.addEventListener("load", function() {

  // Check for cid param and scroll to that comment.
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const cid = urlParams.get('cid')
  var objDiv = document.getElementById("chat");
  if (cid) {
    var commentDiv = document.getElementById(cid);
    commentDiv.scrollIntoView();
    commentDiv.focus();
    commentDiv.classList.add('highlighted');
  }
  else if (objDiv) {
    // Else scroll to bottom of chat window on load.
    objDiv.scrollTop = objDiv.scrollHeight;
    document.getElementById("comment-body").focus();
  }

  // Show menu on header click.
  var header = document.getElementById('contentheader');
  var content = document.getElementById('content');
  header.onclick = function() {
    content.classList.toggle("show-mobile-menu");
  };

  // Disable form for visual indication
  // and to prevent double posts.
  var form = document.getElementById('comment-form');
  form.onsubmit = function() {
    document.getElementById('submit-button').classList.remove('active');
    document.getElementById('comment-body').readonly = true;
    document.getElementById('comment-link').readonly = true;
    document.getElementById('submit-button').disabled = true;
    return true;
  };

  // Submit with ctrl+enter key combo.
  var modifier = false;
  var ctrl = 17;
  var enter = 13;
  form.onkeydown = function(e){
      if(e.keyCode == enter){
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

});

// @todo scroll back to bottom of window when keyboard goes away.
window.visualViewport.addEventListener('resize', function() {
  console.log("resize");
});
