<?php
  session_start();
  require_once('settings.php');

  if (isset($_REQUEST['logout'])) {
      $_SESSION['AUTH'] = false;
      define('AUTH', false);
  }

  else {
      if (isset($_SESSION['AUTH']) && $_SESSION['AUTH'] === true) {
          define('AUTH', true);
      }
      else if (isset($_REQUEST['username']) && !empty($_REQUEST['username']) && isset($_REQUEST['password']) && !empty($_REQUEST['password'])) {
          $username = $_REQUEST['username'];
          $password = $_REQUEST['password'];
          if ($username === $SETTINGS->username && $password === $SETTINGS->password) {
              $_SESSION['AUTH'] = true;
              define('AUTH', true);
          }
          else {
              $_SESSION['AUTH'] = false;
              define('AUTH', false);
          }
      }
      else {
          $_SESSION['AUTH'] = false;
          define('AUTH', false);
      }
  }

?>