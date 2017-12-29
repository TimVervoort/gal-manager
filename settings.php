<?php
session_start();

$user = 'Marc Sterken';
$password = 'lastrada';

if ((isset($_REQUEST['user'])) && (isset($_REQUEST['password']))) {
  if (($_REQUEST['user'] === $user) && ($_REQUEST['password'] === $password)) {
    $_SESSION['login'] = true;
  }
}

function isLoggedIn() {
  return (isset($_SESSION['login']) && $_SESSION['login'] === true);
}

$uploads = 'uploads/'; //Folder where the original images are to be uploaded
$uploadWidth = 1000; //Resize the uploaded images to this width
$thumbnails = 'thumbs/'; //Folder where the thumbnails are to be stored
$thumbnailWidth = 200; //Resize the thumbnails to this width

//Remark that the two folders above do need to have write permission (chmod to 777)
?>