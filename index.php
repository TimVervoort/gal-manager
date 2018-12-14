<?php
    require_once('inc/login_check.php');
    require_once('inc/settings.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <title>Manage galleries - <?php echo $SETTINGS->siteName; ?></title>
        <link rel="stylesheet" href="css/styles.css" />
        <link rel="stylesheet" href="css/login.css" />
        <link rel="stylesheet" href="css/dashboard.css" />
    </head>
    <body>

        <?php
            if (AUTH !== true) {    
                require_once('inc/login_form.php'); // Display gallery manager
            }
            else {  
                require_once('inc/dashboard.php'); // Display login form
            } 
        ?>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <a href="<?php echo $SETTINGS->siteUrl; ?>"><?php echo $SETTINGS->siteName; ?></a></p>
        </footer>
        
    </body>
</html>