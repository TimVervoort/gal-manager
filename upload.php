<?php

require_once('settings.php');

if (!isset($_FILES['file'])) {
  die('No file(s) selected.');
}

for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

  $extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
  $ext = explode('.', basename($_FILES['file']['name'][$i]));
  $extension = strtolower(end($ext));
  $filename = strtolower(basename($_FILES['file']['name'][$i]));
  $target = $uploads.$filename;

  if ($_FILES["file"]["size"][$i] > (10000*10000)) {
    die('Image is too large.');
  }

  if (!in_array($extension, $extensions)) {
    die('Not a valid image extension.');
  }

  if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target)) {
    echo '<div class="success">'.$filename.'</div>';
    createThumbnail($target, $target, 1000); 
    createThumbnail($target, $thumbnails.$filename, 200);         
  } 
  else {
    echo '<div class="error">Server problem. Could not upload '.$filename.'.</div>';
  }

}

function createThumbnail($src, $dest, $desired_width) {

  //read the source image
  $source_image = imagecreatefromjpeg($src);
  $width = imagesx($source_image);
  $height = imagesy($source_image);
	
  //find the "desired height" of this thumbnail, relative to the desired width
  $desired_height = floor($height * ($desired_width / $width));
	
  //create a new, "virtual" image
  $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	
  //copy source image at a resized size
  imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	
  //create the physical thumbnail image to its destination
  imagejpeg($virtual_image, $dest);
}

?>