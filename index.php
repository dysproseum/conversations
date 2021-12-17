<?php
  // Redirect to dashboard if logged in.
  session_start();
  if (isset($_SESSION['sub'])) {
    header('Location: /conversations/dashboard.php');
    exit;
  }
?>

<html>
<head>
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body class="home">
  <div id="content">
    <h1>Hello</h1>
    Welcome to Conversations! <a href="login.php">Login with Google</a>
  </div>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-4383228-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-4383228-1');
</script>


</body>
</html>
