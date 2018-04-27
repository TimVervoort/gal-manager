<?php
session_start();

$user = 'admin';
$password = 'test';

if ((isset($_REQUEST['user'])) && (isset($_REQUEST['password']))) {
  if (($_REQUEST['user'] === $user) && ($_REQUEST['password'] === $password)) {
    $_SESSION['login'] = true;
  }
}

function isLoggedIn() {

  return true;

  return (isset($_SESSION['login']) && $_SESSION['login'] === true);
}

if (isLoggedIn()) {
  require_once('manager.php');
}
else {
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <title>Gallery Manager</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <style type="text/css">
      @import url('https://fonts.googleapis.com/css?family=Roboto');
      html, body {font-family: 'Roboto', sans-serif;height:100%;width:100%;margin:0;padding:0;overflow:hidden;background:#111;color:#fff;text-align:center;font-weight:300;display:flex;flex-direction:column;justify-content:center;}
      form {width:100%;height:auto;overflow:auto;marign:0;padding:0;}
      input {box-sizing:border-box;width:90%;max-width:400px;margin:20px auto;text-align:center;border:0;background:#eee;color:#123;clear:both;display:block;overflow:hidden;height:50px;line-height:50px;}
      a:link, a:visited {color:#fff;text-decoration:underline;}
    </style>
  </head>
  <body>
    <h1>Login to edit galleries</h1>
    <form action="index.php" method="post">
      <input type="text" name="user" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="submit" value="Login" />
    </form>
    <p>&copy; <a href="https://www.timvervoort.com">Tim Vervoort</a></p>
  </body>
</html>
<?php
  die();
}

$uploads = 'uploads/'; //Folder where the original images are to be uploaded
$uploadWidth = 1000; //Resize the uploaded images to this width
$thumbnails = 'thumbs/'; //Folder where the thumbnails are to be stored
$thumbnailWidth = 200; //Resize the thumbnails to this width

//Remark that the two folders above do need to have write permission (chmod to 777)
?>