<?php 
include_once "common/base.php";


/**
 * Be forewarned - this document will start with an invisible character (BOM)
 * because I used include.
**/


if(!isset($_GET['timestamp'])){
    exit();
}
$timestamp = $_GET['timestamp'];
//echo $timestamp;
//ensure that the page received a valid timestamp
if(!(((string) (int) $timestamp === $timestamp) 
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX))){
    exit();
}
include_once "common/inc/class.schedule_items.inc.php";
$schedule = new ClassScheduleItems($db);
$day_info = $schedule->getSchoolDayInfo(date('Y-m-d',$timestamp));
if($day_info[1]=='Error'){
     echo $day_info[0];
    // include_once("common/footer.php");
    exit();
}
$class_day = $day_info[0]; 
$bell_schedule = $day_info[1];
$bell_times = $schedule->getBellsNew($bell_schedule);
$json_bells = json_encode($bell_times);
//echo "<pre>".print_r($bell_times,true)."</pre>";
if(count($bell_times)<2){
    echo "Error obtaining times for bell schedule named $bell_schedule.";
    // include_once("common/footer.php");
    exit();
}
if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1){
$class_names = $schedule->getClassesNew($class_day);
//echo "<pre>".print_r($class_names,true)."</pre>";
if(count($class_names)<1){ 
    //echo "Your schedule for this date appears to be empty.";
}
$json_classes = json_encode($class_names);

//echo "var classes = $json_classes;";
}
else $json_classes="[]";
if($_GET['v']==2){
echo $json_classes;
}

// $dateformat = date('l, F jS', $dateviewing);
// echo "var dateName = \"$dateformat\";";

// echo "var schedule = {\"tues-thurs\": $json_bells}";
if($_GET['v']==1){
echo "$json_bells";
}






exit();
?>