<?php
 
    session_start();
 
    unset($_SESSION['LoggedIn']);
    unset($_SESSION['Username']);
    unset($_SESSION['IsAdmin']);
    session_unset();
    session_destroy();
 
?>
 
<meta http-equiv="refresh" content="0;index.php">