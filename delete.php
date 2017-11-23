<?php
require_once('settings.php');

if (!isset($_REQUEST['img'])) { die('No filename given.'); }

$filename = $_REQUEST['img'];

if (strpos($filename, '..')) {
  die('Could not delete image outside images folder.');
}

if (unlink($uploads.$filename)) {
  echo 'Image '.$filename.' deleted.';
}
else {
  die('Could not delete image '.$filename);
}

if (unlink($thumbnails.$filename)) { }
else {
  die('Could not delete thumbnail '.$filename);
}
?>