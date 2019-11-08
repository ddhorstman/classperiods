<?php 
include_once "common/base.php";
$pageTitle = "Daily Schedule";
include_once "common/header.php"; ?>
<!-- jQuery UI Datepicker -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
  
  
<div id="main">
	<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
}

</style>

<?php
    if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1):
        if(isset($_POST['tomorrow'])){
            $_SESSION['date_offset']+= $_POST['tomorrow'];
        }
            $dateviewing = strtotime("+".$_SESSION['date_offset']." day");
            if(isset($_POST['datepicker'])){
                $dateviewing = strtotime( $_POST['datepicker']);
            }

?>
               
               <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <br></h2><form action='daily_schedule.php' method='post' autocomplete="off">
   <label for="datepicker">Select a different day:</label> <input type="text" id = "datepicker" name = "datepicker">
    <input type="submit" value = "Change date">
 <!-- <button name='tomorrow' value='0'>Today</button>
  <button name='tomorrow' value='1'>Tomorrow</button> -->
  </form>
  <h1 align="center"><?php echo date('l, F jS', $dateviewing);?></h1>
               


<?php 
include_once "common/inc/class.schedule_items.inc.php";
$schedule = new ClassScheduleItems($db);
$day_info = $schedule->getSchoolDayInfo(date('Y-m-d',$dateviewing));
if($day_info[1]=='Error'){
    echo $day_info[0];
    include_once("common/footer.php");
    exit();
}
$class_day = $day_info[0]; 
$bell_schedule = $day_info[1];
$bell_times = $schedule->getBellSchedule($bell_schedule);
//for($i=0;$i<count($bell_times);$i++)echo "$bell_times[$i]<br>";
if(count($bell_times)<2){
    echo "<h2>Error obtaining times for bell schedule named $bell_schedule.</h2>";
    include_once("common/footer.php");
    exit();
}
$class_names = $schedule->getClassNames($class_day);
if(count($class_names)<2){ 
    echo "<h2>Your schedule for this date appears to be empty.</h2>";
    $class_names = ["",""];
}
//for($i=0;$i<count($class_names);$i++)echo "$i: $class_names[$i]<br>";
?>

<table align="center">
	
	<tr>
		<th>Period</th>
		<th>Class</th>
		</tr>

<?php 
for($i=0;$i<count($bell_times);$i+=3){
    $j = $i +1; //for start time
   $k = $i + 2; //for end time
   echo "<tr><td><b>$bell_times[$i]:</b>";
  //if(strlen($bell_times[$i])>3) echo "<br>";
   echo " $bell_times[$j] - $bell_times[$k]</td><td><b>";
   if(in_array($bell_times[$i],$class_names)){
      $m = array_search($bell_times[$i],$class_names)+1;

      echo "$class_names[$m]";
   }
   
   echo "
   </b></td>
   </tr>" ;
}
?>


</table>

<?php else: ?>
                <h1>Not logged in!</h1>
<?php endif; ?>


</div>

<?php include_once "common/sidebar.php"; ?>