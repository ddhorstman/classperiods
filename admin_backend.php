<?php 

include_once "common/base.php";
/**
 * Be forewarned - this document will start with an invisible character (BOM)
 * because I used include.
**/
if(!(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1)){
	//echo "Not logged in";
	exit();
}
if(!isset($_GET['r'])){
	//echo "No request specified";
	exit();
}
include_once "common/inc/class.school_calendar.inc.php";
$schoolCal = new SchoolCalendar($db);
if(!$schoolCal->validateAdmin()){
	//echo "Not an administrator";
	exit();
}

if($_GET['r']=='0'&&isset($_GET['m'])){
	echo "caldata=";
	echo json_encode($schoolCal->getvalidSchoolDays($_GET['m']));
}

else if($_GET['r']==1&&isset($_GET['d'])&&isset($_GET['c'])&&isset($_GET['b'])){
	$date = $_GET['d'];
	$class_schedule = $_GET['c'];
	$bell_schedule = $_GET['b'];
	echo "schoolDayUpdate=";
	echo $schoolCal->updateSchoolDay($date,$class_schedule,$bell_schedule);

}


?>