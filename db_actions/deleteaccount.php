<?php

session_start();

include_once "../common/inc/constants.inc.php";
include_once "../common/inc/class.users.inc.php";
$userObj = new CalendarUsers();

if(isset($_SESSION['LoggedIn'])
&& $_SESSION['LoggedIn']==1)
{
    $userObj->deleteAccount();

    }
    
    ?>