# gal-manager
Simple online website gallery manager

# How to use
Use the database_setup.sql file to initialize the database. Create a table, user and password.
Store thes credentials in the settings.php file (see comments).
Make sure the uploads and thumbs folder have write permission, chmod to +w.
Create a username and password in the settings.php for the administrator.
Navigate with your browser to index.php and everything should be ready to go.

# Functionalities
Upload and resize images, create thumbnails, upload video's, import images and video's from websites, swap order from images, create galleries, manage galleries and display galleries.

# Display a gallery
Include the handler.php file somewhere on the page where you want to display one more galleries. Replace album ID with the actual ID of the image. Include the following snippet:
```php
<?php
    require_once('inc/handler.php');
    displayAlbum(ALBUM_ID);
?>
```
Don't know the ID of the gallery? Look it up in the database or navigate to handler.php with your browser, it should display a JSON with all the available galleries.
The JSON can also be used to display galleries without the use of PHP.

# Future functionalities
- Generate a thumbnail for a video.
- Responsive dashboard.
- Multilanguage dashboard.
- Enforce storage limit (front & back end) and display usage.