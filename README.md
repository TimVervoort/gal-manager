# gal-manager
Simple online website gallery manager

# How to use
Use the database_setup.sql file to initialize the database. Create a table, user and password.
Store thes credentials in the manager.php file (see comments).
Make sure the uploads and thumbs folder have write permission, chmod to 777.
Create a username and password in the settings.php for the administrator.
Go to index.php and everything should be ready to go.

# Functionalities
Upload and resize images, create thumbnails, create galleries, manage galleries and display galleries.

# Display a gallery on the website
NOTE: there is a bug, the user should be logged in to view the gallery, otherwise the login form is displayed. This will be fixed in the next commit.
A gallery is retrieved by calling the getGalleryContents(gallery) function where gallery is the gallery identifier (see database for the identifier).
The function returns a list of key value pair 'image' => filename.
Example:
```php
<?php
$manager = new GalleryManager();
$images = $manager->getGalleryContents(0);
foreach ($images as $img) {
  echo '<img src="'.$uploads.$img['img']." />';
}
?>
```
 
# Comming soon
Swap images to change order.
Rename galleries.
