<?php 
include_once "common/base.php";
/**
 * Be forewarned - this document will start with an invisible character (BOM)
 * because I used include.
**/
if(isset($_POST['c'])&&isset($_POST['p'])){
    $classname=$_POST['c'];
    $period=$_POST['p'];
}
else {
    exit();
}
include_once "common/inc/class.schedule_items.inc.php";
$schedule = new ClassScheduleItems($db);
$message= $schedule->updateScheduleItem($period,$classname);
echo $message;
exit();
?>