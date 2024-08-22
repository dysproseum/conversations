// Browser Notifications

function notifyMe(comment) {

  let notification;
  let options = {
    icon: "https://dysproseum.com/favicon.ico",
    body: comment.body,
  };
  let title = comment.parent_title;

  if (!("Notification" in window)) {
    // Check if the browser supports notifications
    alert("This browser does not support desktop notification");
  } else if (Notification.permission === "granted") {
    // Check whether notification permissions have already been granted;
    // if so, create a notification
    notification = new Notification(title, options);
  } else if (Notification.permission !== "denied") {
    // We need to ask the user for permission
    Notification.requestPermission().then((permission) => {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        notification = new Notification(title, options);
      }
    });
  }

  if (notification) {
    notification.onclick = function() {
      var url = 'https://dysproseum.com/conversations/post.php';
      window.open(url + '?id=' + comment.parent_id + '&cid=' + comment.id);
      notification.close();
    };
  }

  // At last, if the user has denied notifications, and you
  // want to be respectful there is no need to bother them anymore.
}
