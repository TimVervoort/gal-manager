<?php
require_once('settings.php');

class GalleryManager {

  private $db = null;
  private $host = ''; // Host of the database
  private $table = ''; // Database table
  private $user = ''; // Database user
  private $password = ''; // Database password

  public function __construct() {
     $dns = 'mysql:host='.$this->host.';dbname='.$this->table.';charset=utf8;';
     $this->db = new PDO($dns, $this->user, $this->password);
     $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function __destruct() {
    $this->db = null;
  }

  public function getGalleries() {
    $gals = array();
    $sql = '
      SELECT *
      FROM Gallery
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
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

  public function getGalleryContents($gal) {
    $imgs = array();
    $sql = '
      SELECT *
      FROM GalleryContents
      WHERE gallery = :gal
      ORDER BY number ASC
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':gal', $gal, PDO::PARAM_STR);
      $query->execute();
      $r = $query->fetchAll(PDO::FETCH_NAMED);
      foreach ($r as $i) {
        $img = ['img' => $i['image'], 'number' => $i['image']];
        array_push($imgs, $img);
      }
    }
    catch(PDOException $e) { echo $e->getMessage(); }
    return $imgs;
  }
  
  public function addToGallery($img, $gal) {
    $sql = '
      INSERT INTO GalleryContents (gallery, image, number)
      VALUES (:gal, :img, :nr)
    ';
    $id = $this->createNewImageNr($gal);
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':gal', $gal, PDO::PARAM_STR);
      $query->bindparam(':img', $img, PDO::PARAM_STR);
      $query->bindparam(':nr', $id, PDO::PARAM_INT);
      $query->execute();
    }
    catch(PDOException $e) { echo $e->getMessage(); }
  }
  
  private function createNewImageNr($gal) {
    $sql = '
      SELECT COUNT(image) AS Nr
      FROM GalleryContents
      WHERE gallery = :gal
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
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
  
  public function switchOrder($gal, $imgA, $imgB) {
    $temp = $this->getOrder($gal, $imgA);
    $this->setOrder($gal, $imgA, $this->getOrder($gal, $imgB));
    $this->setOrder($gal, $imgB, $temp);
  }
  
  private function getOrder($gal, $img) {
    $order = 0;
    $sql = '
      SELECT number
      FROM GalleryContents
      WHERE gallery = :gal AND image = :img
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
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

  private function setOrder($gal, $img, $order) {
    $sql = '
      UPDATE GalleryContents
      SET number = :order
      WHERE gallery = :gal AND image = :img
    ';  
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':gal', $gal, PDO::PARAM_INT);
      $query->bindparam(':img', $img, PDO::PARAM_STR);
      $query->bindparam(':order', $order, PDO::PARAM_INT);
      $query->execute();
    }
    catch(PDOException $e) { echo $e->getMessage(); }
  }
  
  public function removeFromGallery($img, $gal) {
    $sql = '
      DELETE FROM GalleryContents
      WHERE gallery = :gal AND image = :img
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':gal', $gal, PDO::PARAM_STR);
      $query->bindparam(':img', $img, PDO::PARAM_STR);
      $query->execute();
    }
    catch(PDOException $e) { echo $e->getMessage(); }
  }

  private function createGalleryID() {
    $sql = '
      SELECT id As Nr
      FROM Gallery
      ORDER BY id DESC
      LIMIT 1
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
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
  
  public function createGallery($name) {
    $id = $this->createGalleryID();
    $sql = '
      INSERT INTO Gallery (id, name)
      VALUES (:id, :name)
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':id', $id, PDO::PARAM_STR);
      $query->bindparam(':name', $name, PDO::PARAM_STR);
      $query->execute();
    }
    catch(PDOException $e) { echo $e->getMessage(); }
  }
  
  public function deleteGallery($gal) {
    $sql = '
      DELETE FROM GalleryContents
      WHERE gallery = :gal
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':gal', $gal, PDO::PARAM_STR);
      $query->execute();
    }
    catch(PDOException $e) { echo $e->getMessage(); }
    $sql = '
      DELETE FROM Gallery
      WHERE id = :gal
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':gal', $gal, PDO::PARAM_STR);
      $query->execute();
    }
    catch(PDOException $e) { echo $e->getMessage(); }  
  }
  
  public function changeGalleryName($gal, $name) {
    $sql = '
      UPDATE Gallery
      SET name = :name
      WHERE id = :gal
    ';
    try {
      $query = $this->db->prepare($sql);
      $this->db->prepare("SET CHARSET utf8");
      $query->bindparam(':gal', $gal, PDO::PARAM_STR);
      $query->bindparam(':name', $name, PDO::PARAM_STR);
      $query->execute();
    }
    catch(PDOException $e) { echo $e->getMessage(); }
  }

}

$manager = new GalleryManager();

if (isset($_REQUEST['create'])) {
  $name = $_REQUEST['name'];
  $manager->createGallery($name);
}

if (isset($_REQUEST['add'])) {
  $image = $_REQUEST['img'];
  $gallery = $_REQUEST['gal'];
  $manager->addToGallery($image, $gallery);
}

if (isset($_REQUEST['delete'])) {
  $gallery = $_REQUEST['gal'];
  if (isset($_REQUEST['img'])) {
    $image = $_REQUEST['img'];
    $manager->removeFromGallery($image, $gallery);
  }
  else {
    $manager->deleteGallery($gallery);
  }
}

if (isset($_REQUEST['switch'])) {
  $gal = $_REQUEST['gal'];
  $imgA = $_REQUEST['imgA'];
  $imgB = $_REQUEST['imgB'];
  $manager->switchOrder($gal, $imgA, $imgB);
}
?>