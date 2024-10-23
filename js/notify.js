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

function updatePrompt() {
  if ('Notification' in window) {
    if (Notification.permission == 'granted' || Notification.permission == 'denied') {
      notify_enable.hidden = true;
    } else {
      notify_enable.hidden = false;
    }
  }
}

function onPromptClick(obj) {
  if ('Notification' in window) {
    obj.labels[0].textContent = "Click Allow in your browser's popup";
    obj.hidden = true;

    Notification.requestPermission().then((permission) => {
      updatePrompt();
      if (permission === 'granted') {
        console.log('Notification permission granted.');
        obj.labels[0].hidden = true;

        enableRadios();
        manageAccount("notify_banner=1");
        manageAccount("notify_sound=1");
        var test = document.getElementById("notify_test");
        test.disabled = false;
      }
      else if (permission === 'denied') {
        console.warn('Notification permission denied.');
        obj.labels[0].textContent = "Notifications are disabled in your browser";
      }
    });
  }
}

function getNotificationPermission() {
  if ('Notification' in window) {
    switch (Notification.permission) {
      case 'granted':
        return true;
      case 'denied':
      case 'default':
        return false;
    }
  }
}

function enableRadios() {
  var n = document.getElementsByName("notify_banner");
  for (i=0; i < n.length; i++) {
    n[i].disabled = false;
  }

  var s = document.getElementsByName("notify_sound");
  for (i=0; i < s.length; i++) {
    s[i].disabled = false;
  }
}

function manageAccount(params) {
  var url='https://dysproseum.com/conversations/manage_account.php';

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == XMLHttpRequest.DONE) {
      if (this.status == 200) {
        var data = JSON.parse(this.responseText);

        // Set preferences.
        for (var i in data) {
          var key = i;
          var val = data[i];
          var pref = document.getElementById(key + "_" + val);
          pref.checked = true;
        }
      }
    }
  };
  if (params) {
    xhttp.open("POST", url, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(params);
  }
  else {
    xhttp.open("GET", url, true);
    xhttp.send();
  }
}

window.addEventListener("load", function() {
  var enable = document.getElementById("notify_enable");
  var test = document.getElementById("notify_test");
  if (getNotificationPermission()) {
    enable.hidden = true;
    test.disabled = false;
    enableRadios();
    manageAccount();
  }
  else {
    if (notify == 1) {
      // reset permission in database?
    }
  }

  enable.addEventListener("click", function(e) {

    if (getNotificationPermission() == false) {
      onPromptClick(this);
    }

    e.preventDefault();
    return false;
  });

  var banner = document.getElementsByName("notify_banner");
  for (i=0; i < banner.length; i++) {
    banner[i].addEventListener("click", function() {
      manageAccount("notify_banner=" + this.value);
    });
  }

  var sound = document.getElementsByName("notify_sound");
  for (i=0; i < sound.length; i++) {
    sound[i].addEventListener("click", function() {
      manageAccount("notify_sound=" + this.value);
    });
  }

  test.addEventListener("click", function() {
    comment = new Object();
    comment.body = "Lorem ipsum";
    comment.parent_title = "Test notification"
    notifyMe(comment);

    // if (notifyaudio) {
    notifyaudio.play();
  });

});
