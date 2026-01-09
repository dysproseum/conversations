window.addEventListener("load", function() {

  // Links to post from buddy list.
  var postLinks = document.querySelectorAll(".sidebar .post a");
  for (const link of postLinks) {
    link.addEventListener("click", function(e) {
      e.preventDefault();
      var url = this.href;

      var parsedUrl = new URL(url);
      var id = parsedUrl.searchParams.get('id');
      url = url.replace('conversations', 'conversations/iframe');

      // open new window, or switch to window
      parent.openIframeWindow("conversations_post", id, url);
      return false;
    });
  }

  // Post submit comment.
  var form = document.querySelector("form#comment-form");
  if (form) {
    var url = form.action;
    form.addEventListener("submit", function(e) {
     // e.preventDefault();
      console.log(e);
    //  return false;
    });
  }
});
