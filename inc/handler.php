<?php

  // Allow access for this API
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
  header('Content-type: application/json; charset=utf-8');

  require_once('login_check.php');

  // Init database connection
  $dns = 'mysql:host='.$SETTINGS->dbhost.';dbname='.$SETTINGS->dbtable.';charset=utf8;';
  $db = new PDO($dns, $SETTINGS->dbuser, $SETTINGS->dbpassword);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  function getDirectorySize($path){
      $bytestotal = 0;
      $path = realpath($path);
      if ($path!==false && $path!='' && file_exists($path)){
          foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
              $bytestotal += $object->getSize();
          }
      }
      return $bytestotal;
  }

  // =====================================================================================================================
  // Global functions
  // =====================================================================================================================
  function randomString($length = 10) {
      return substr(str_shuffle(MD5(microtime())), 0, $length);
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

  // Get url from embed code (iframe, video tag etc.)
  function getUrlFromInput($str) {
      preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $str, $match);
      if (isset($match[0]) && isset($match[0][0])) {
          return $match[0][0];
      }
      return '';
  }

  function getAllGalleries() {
    global $db;
    $gals = array();
    $sql = '
        SELECT *
        FROM Gallery
    ';
    try {
        $query = $db->prepare($sql);
        $db->prepare("SET CHARSET utf8");
        $query->execute();
        $r = $query->fetchAll(PDO::FETCH_NAMED);
        foreach ($r as $i) {
            $name = $i['name'];
            $id = $i['id'];
            $gal = ['id' => $id, 'name' => $name];
            array_push($gals, $gal);
        }
    }
    catch(PDOException $e) { echo $e->getMessage(); }
    return $gals;
  }

  function getGalleryContents($gal) {
      global $db;
      $imgs = array();
      $sql = '
          SELECT *
          FROM GalleryContents
          WHERE gallery = :gal
          ORDER BY number ASC
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->execute();
          $r = $query->fetchAll(PDO::FETCH_NAMED);
          foreach ($r as $i) {
              $img = ['img' => $i['image'], 'nr' => $i['number']];
              if (strpos($i['image'], '.mp4') !== false || strpos($i['image'], '.webm') !== false) {
                  $img = ['vid' => $i['image'], 'nr' => $i['number']];
              }
              if (strpos($i['image'], '.mp4') === false && strpos($i['image'], '.webm') === false && strpos($i['image'], 'http') !== false) {
                  $img = ['iframe' => $i['image'], 'nr' => $i['number']];
              }
              array_push($imgs, $img);
          }
      }
      catch(PDOException $e) { echo $e->getMessage(); }
      return $imgs;
  }

  function removeFromGallery($img, $gal) {
      global $db;
      $sql = '
          DELETE FROM GalleryContents
          WHERE gallery = :gal AND image = :img
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->bindparam(':img', $img, PDO::PARAM_STR);
          $query->execute();
      }
      catch(PDOException $e) { echo $e->getMessage(); }
  }

  function createGallery($name) {
      global $db;
      $id = createGalleryID();
      $sql = '
        INSERT INTO Gallery (id, name)
        VALUES (:id, :name)
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':id', $id, PDO::PARAM_STR);
          $query->bindparam(':name', $name, PDO::PARAM_STR);
          $query->execute();
      }
      catch(PDOException $e) { echo $e->getMessage(); }
      return $id;
  }

  function addToGallery($img, $gal) {
      global $db;
      $sql = '
          INSERT INTO GalleryContents (gallery, image, number)
          VALUES (:gal, :img, :nr)
      ';
      $id = createNewImageNr($gal);
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->bindparam(':img', $img, PDO::PARAM_STR);
          $query->bindparam(':nr', $id, PDO::PARAM_INT);
          $query->execute();
      }
      catch(PDOException $e) { echo $e->getMessage(); }
  }

  function createNewImageNr($gal) {
      global $db;
      $sql = '
          SELECT COUNT(image) AS Nr
          FROM GalleryContents
          WHERE gallery = :gal
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->execute();
          $r = $query->fetchAll(PDO::FETCH_NAMED);
          foreach ($r as $i) {
              $id = $i['Nr'];
          }
      }
      catch(PDOException $e) { echo $e->getMessage(); }
      return $id + 1;
  }

  function switchOrder($gal, $imgA, $imgB) {
      global $db;
      $temp = getOrder($gal, $imgA);
      setOrder($gal, $imgA, getOrder($gal, $imgB));
      setOrder($gal, $imgB, $temp);
  }

  function getOrder($gal, $img) {
      global $db;
      $order = 0;
      $sql = '
          SELECT number
          FROM GalleryContents
          WHERE gallery = :gal AND image = :img
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_INT);
          $query->bindparam(':img', $img, PDO::PARAM_STR);
          $query->execute();
          $r = $query->fetchAll(PDO::FETCH_NAMED);
          foreach ($r as $i) {
              $order = $i['number'];
          }
      }
      catch(PDOException $e) { echo $e->getMessage(); }
      return $order;
  }

  function setOrder($gal, $img, $order) {
      global $db;
      $sql = '
          UPDATE GalleryContents
          SET number = :order
          WHERE gallery = :gal AND image = :img
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_INT);
          $query->bindparam(':img', $img, PDO::PARAM_STR);
          $query->bindparam(':order', $order, PDO::PARAM_INT);
          $query->execute();
      }
      catch(PDOException $e) { echo $e->getMessage(); }
  }

  function createGalleryID() {
      global $db;
      $sql = '
          SELECT id As Nr
          FROM Gallery
          ORDER BY id DESC
          LIMIT 1
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->execute();
          $r = $query->fetchAll(PDO::FETCH_NAMED);
          foreach ($r as $i) {
              $id = $i['Nr'];
          }
      }
      catch(PDOException $e) { echo $e->getMessage(); }
      return $id + 1;
  }

  function deleteGallery($gal) {
      global $db;
      $sql = '
          DELETE FROM GalleryContents
          WHERE gallery = :gal
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->execute();
      }
      catch(PDOException $e) { echo $e->getMessage(); }
      $sql = '
          DELETE FROM Gallery
          WHERE id = :gal
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->execute();
      }
      catch(PDOException $e) { echo $e->getMessage(); }
  }

  function changeGalleryName($gal, $name) {
      global $db;
      $sql = '
          UPDATE Gallery
          SET name = :name
          WHERE id = :gal
      ';
      try {
          $query = $db->prepare($sql);
          $db->prepare("SET CHARSET utf8");
          $query->bindparam(':gal', $gal, PDO::PARAM_STR);
          $query->bindparam(':name', $name, PDO::PARAM_STR);
          $query->execute();
      }
      catch(PDOException $e) { echo $e->getMessage(); }
  }

  // =====================================================================================================================
  // Display all galleries
  // =====================================================================================================================
  if (isset($_REQUEST['all'])) {
      $res = new stdClass();
      $res->galleries = array();
      $gals = getAllGalleries();
      foreach ($gals as $g) {
          $imgs = getGalleryContents($g['id']);
          $gal = new stdClass();
          $gal->id = $g['id'];
          $gal->name = $g['name'];
          $gal->content = $imgs;
          array_push($res->galleries, $gal);
      }
      echo json_encode($res);
      exit();
  }
  else if (isset($_REQUEST['usage'])) {
      $max = 1024 * 1024 * $SETTINGS->maxStorageMB;
      $used = getDirectorySize($SETTINGS->originalDir) + getDirectorySize($SETTINGS->thumbDir);
      $percent = $used / $max * 100;
      echo round($percent).'%';
      exit();
  }

  if (!AUTH) {
      die('Login first.');
  }

  // =====================================================================================================================
  // Display gallery
  // =====================================================================================================================
  else if (isset($_REQUEST['display']) && !empty($_REQUEST['display'])) {
      $gals = getGalleryContents($_REQUEST['display']);
  }

  // =====================================================================================================================
  // Create gallery
  // =====================================================================================================================
  else if (isset($_REQUEST['newGallery']) && !empty($_REQUEST['newGallery'])) {
      $id = createGallery($_REQUEST['newGallery']);
      $res = new stdClass();
      $res->id = $id;
      echo json_encode($res);
  }

  // =====================================================================================================================
  // Rename gallery
  // =====================================================================================================================
  else if (isset($_REQUEST['rename']) && !empty($_REQUEST['rename']) && isset($_REQUEST['new']) && !empty($_REQUEST['new'])) {
      changeGalleryName($_REQUEST['rename'], $_REQUEST['new']);
  }

  // =====================================================================================================================
  // Delete gallery
  // =====================================================================================================================
  else if (isset($_REQUEST['deleteGallery']) && !empty($_REQUEST['deleteGallery'])) {
      deleteGallery($_REQUEST['deleteGallery']);
  }

  // =====================================================================================================================
  // Upload images or videos
  // =====================================================================================================================

  else if (isset($_FILES['file']) && isset($_REQUEST['uploadTo']) && !empty($_REQUEST['uploadTo'])) {
      $imgs = array();
      for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
          $extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm');
          $ext = explode('.', basename($_FILES['file']['name'][$i]));
          $extension = strtolower(end($ext));
          $filename = strtolower(basename($_FILES['file']['name'][$i]));
          $newName = randomString().'.'.$extension;
          $target = $SETTINGS->originalDir.$newName;
        
          if (!in_array($extension, $extensions)) {
              die('Not a valid image extension. Only .jpg, .jpeg, .png, .gif and .webp extensions are allowed.');
          }
          // It's a picture
          else {
              if ($extension !== 'mp4' && $extension !== 'webm' && $_FILES["file"]["size"][$i] > (10000*10000)) { /// TODO ENFORCE FILE SIZE
                  die('Image is too large. Resize or crop the image below 10.000 x 10.000 pixels.');
              }
          }
        
          if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target)) {
              if ($extension !== 'mp4' && $extension !== 'webm') { // Create thumbnails from images, not from videos.
                  createThumbnail($target, $target, 1000); 
                  createThumbnail($target, $SETTINGS->thumbDir.$newName, 200);
              }      
              array_push($imgs, $newName);
              addToGallery($newName, $_REQUEST['uploadTo']);
          } 
          else {
              $e = new stdClass();
              $e->error = 'Couldn\'t upload '.$filename.' to server. Reason unkown. Please try again.';
              die(json_encode($e));
          }
      }
      /// TODO HANDLE ERRORS INDIVIDUALY
      $o = new stdClass();
      $o->images = $imgs;
      $o->gallery = $_REQUEST['uploadTo'];
      echo json_encode($o);
  }

  // =====================================================================================================================
  // Delete image
  // =====================================================================================================================
  else if (isset($_REQUEST['delete']) && !empty($_REQUEST['delete']) && isset($_REQUEST['gallery']) && !empty($_REQUEST['gallery'])) {
      $filename = $_REQUEST['delete'];
      // Remove from server
      if (file_exists($SETTINGS->thumbDir.$filename)) {
          unlink($SETTINGS->thumbDir.$filename);
      }
      if (file_exists($SETTINGS->originalDir.$filename)) {
          unlink($SETTINGS->originalDir.$filename);
      }
      // Remove from database
      removeFromGallery($filename, $_REQUEST['gallery']);
  }

  // =====================================================================================================================
  // Swap image
  // =====================================================================================================================
  else if (isset($_REQUEST['swapA']) && !empty($_REQUEST['swapA']) && isset($_REQUEST['swapB']) && !empty($_REQUEST['swapB']) && isset($_REQUEST['gallery']) && !empty($_REQUEST['gallery'])) {
      switchOrder($_REQUEST['gallery'], $_REQUEST['swapA'], $_REQUEST['swapB']);
  }

  // =====================================================================================================================
  // Add image or video from url to gallery
  // =====================================================================================================================
  else if (isset($_REQUEST['addUrl']) && !empty($_REQUEST['addUrl']) && isset($_REQUEST['gallery']) && !empty($_REQUEST['gallery'])) {
      addToGallery(getUrlFromInput($_REQUEST['addUrl']), $_REQUEST['gallery']);
      echo json_encode(getUrlFromInput($_REQUEST['addUrl']));
  }

  // =====================================================================================================================
  else {
      echo 'No action specified. Usage: all';
  }

?>