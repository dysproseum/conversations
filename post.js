window.onload=function () {
  // Scroll to bottom of chat window on load.
  var objDiv = document.getElementById("chat");
  objDiv.scrollTop = objDiv.scrollHeight;

  // Submit with enter key.
  var form = document.getElementById('comment-form');
  document.getElementById('comment-body').onkeydown = function(e){
    if(e.keyCode == 13){
     // submit
     form.submit();
    }
  };
}
