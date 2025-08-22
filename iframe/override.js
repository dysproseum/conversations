window.addEventListener("load", function() {

  // Links to post from buddy list.
  var postLinks = document.querySelectorAll(".sidebar .post a");
  for (const link of postLinks) {
    console.log(link);
    link.addEventListener("click", function(e) {
      e.preventDefault();
      var url = this.href;
      url = url.replace('conversations', 'conversations/iframe');
      console.log(url);
      // open new window, or switch to window
      parent.openIframeWindow(url);
      return false;
    });
  }

  // Post submit comment.
  var form = document.querySelector("form#comment-form");
  var url = form.action;

  form.addEventListener("submit", function(e) {
   // e.preventDefault();
    console.log(e);
  //  return false;
  });
});
