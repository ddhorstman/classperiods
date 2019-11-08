<?php 
include_once "common/base.php";
$pageTitle = "Daily Schedule";
include_once "common/header.php"; ?>
  
<div id="main">

<?php
if(isset($_GET['extID'])){
  include_once "common/inc/class.ics_generation.inc.php";
 $calendar = new iCalGenerator($db);
  $calendar_data = $calendar->generateCalendar($_GET['extID']);
 echo $calendar_data;
}
         ?>      




</div>

<?php include_once "common/sidebar.php"; ?>
<?php include_once "common/footer.php"; ?>