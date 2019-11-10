<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('default_charset','utf-8');

// Start a PHP session
session_start();
$_SESSION['date_offset']=0;
// Include site constants
echo "Testing";
include_once "common/inc/constants.inc.php";

// Create a database object
try {
    $dsn = "mysql:host=".DB_HOST.";dbname="."participating-schools-info";
    $db = new PDO($dsn, DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo "database error";
    //echo '<meta http-equiv="refresh" content="0;URL=https://www.classperiods.com/welcome.php">';
    //echo '</head><body></body></html>';
    //echo 'Connection failed: ' . $e->getMessage();
    exit();
}


?>

Splash screen in development!<br>
You can view the <a href="https://yuhsg.classperiods.com">YUHSG</a> schedule page.