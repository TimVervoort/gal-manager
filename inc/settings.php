<?php

    // Error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // Create settings variables
    $SETTINGS = new stdClass();
    $SETTINGS->username = ''; /// Choose a username to enter the dashboard
    $SETTINGS->password = ''; /// Choose a password to enter the dashboard
    $SETTINGS->originalDir = '../uploads/'; /// Where the original files will be stored (images and video's)
    $SETTINGS->thumbDir = '../thumbs/'; /// Where image thumbnails will be stored
    $SETTINGS->dbhost = ''; /// Database host
    $SETTINGS->dbtable = ''; /// Database table
    $SETTINGS->dbuser = ''; /// Database username
    $SETTINGS->dbpassword = ''; /// Database password
    $SETTINGS->siteName = ''; /// User friendly site name
    $SETTINGS->siteUrl = ''; /// URL to the site where the galleries will be seen
    $SETTINGS->managerUrl = ''; /// The folder where the gallery manager is located
    $SETTINGS->maxUploadMB = 10; /// Maximum upload size in megabytes
    $SETTINGS->maxStorageMB = 500; /// The maximum size for all the galleries combined

?>