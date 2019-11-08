<?php 
include_once "common/base.php";
$pageTitle = "YUHSG Schedule";
include_once "common/header.php"; 
?>
  <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->


  <link rel="stylesheet" type="text/css" href="css/styleCalander.css">
  <!-- <link rel="stylesheet" type="text/css" href="css/styleMenu.css"> -->
  
  
<div id="main">

  <!-- what does this do??? -->
  <!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
    <div id = "errorMessage"></div>
  <div id="datepick">

      <form method='post'>
<!-- <label for="datepicker" id=dateBack>&#8592;</label> -->
<br/>
<div  id="dateBack" style="float: left;" ><img style="width: 15px;height: 20px;"src="js/assets/arrow_left.png" alt="&#8592;"></div>

  
  <?php
  if(isset($_POST['datepicker'])){
      $dateviewing = strtotime( $_POST['datepicker']);
      $date = date('Y-m-d',strtotime($_POST['datepicker']));


  }
  else{ 
    $dateviewing = strtotime("-4 hours", time());
    $date= date('Y-m-d', $dateviewing);
    
  }

  while(date('N', strtotime($date)) >= 6 ){ //saterday or sunday
    // console.log("*************************************************************************")
    $dateviewing += 86400; //add one day 
    $date= date('Y-m-d', strtotime($date. ' + 1 days'));
  }

  // echo "The time is " . date("h:i:sa");

    
  ?>

  
  <!-- <input type="date"

  <?php echo "value=\"$date\""; ?>
  name="datepicker" id ="datepicker"> -->

<!-- <button onclick="getInfoForNewDate()">Submit</button> -->
 <!--  <input type="submit"> -->



  <?php include_once "datePicker.php";
?>

<div  id="dateForward" style="float: left;" ><img style="width: 15px;height: 20px;"src="js/assets/arrow_right.png" alt="&#8594;"></div><br/>
</form>
  </div><br>
  <label class="switch"><input onclick="backgroundColor()" type="checkbox"><span class="slider round"></span></label>

<div id=dayOfWeek style="height: 30px; text-align: left;">Loading...<?php /*echo date('l', $dateviewing);*/
?></div>
  <div id="main-calander"></div>
<script>
  //forward back dates
  document.getElementById("dateForward").addEventListener("click", function(){

    var prevDate=picker.get('select').pick;
    picker.set('select', picker.get('select').pick+86400000);

    var loopCounter=0;
    while(!isValidDate()){
      picker.set('select', picker.get('select').pick+86400000);
      loopCounter++;
      if(loopCounter>30){
        //reset
        picker.set('select',prevDate);
        break;
      }
    }
    
  });
  document.getElementById("dateBack").addEventListener("click", function(){

      var prevDate=picker.get('select').pick;
      picker.set('select', picker.get('select').pick-86400000);

      var loopCounter=0;
      while(!isValidDate()){
        picker.set('select', picker.get('select').pick-86400000);
        loopCounter++;
        if(loopCounter>30){
          //reset
          picker.set('select',prevDate);
          break;
        }
      }
    // }
    
  });



  function getInfoForNewDate(dateSelected_){
    if(!dateSelected_){
    dateSelected_=document.getElementById("datepicker").value;
    }

  var xhttp;
  var classStartID = "classSchedule=";
  var classEndID = "endClassSchedule";
  var bellStartID = "bellSchedule=";
  var bellEndID = "endBellSchedule";
  var abcStartID = "ABC=";
  var abcEndID = "endABC";
  var abcDay = "";
  var receivedData = "";
  var classSchedule = "";
  var bellSchedule = "";

  var datepickerValue = dateSelected_;
  var date = new Date(datepickerValue);
  //for some reason the timestamp has 3 trailing zeroes appended by default - weird
  var str = date.getTime().toString().slice(0,-3);



  xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      receivedData=this.responseText;
      classSchedule = receivedData.substring(receivedData.indexOf(classStartID)+classStartID.length, receivedData.indexOf(classEndID));
      bellSchedule = receivedData.substring(receivedData.indexOf(bellStartID)+bellStartID.length, receivedData.indexOf(bellEndID));
      abcDay = receivedData.substring(receivedData.indexOf(abcStartID)+abcStartID.length, receivedData.indexOf(abcEndID));
       document.getElementById("errorMessage").innerHTML="";
       schedule = JSON.parse(bellSchedule);
       classes = JSON.parse(classSchedule);
       console.log(schedule);
       console.log(classes);
       console.log(abcDay);
       reInitializeScheduleView(abcDay);
    }
  };
   xhttp.open("GET", "respondToDatepicker.php?v=3&timestamp="+str, true);
  //v=1 - return periods
  xhttp.send('');

  }
/*
  function getInfoForNewDateOld(dateSelected_){
  if(!dateSelected_){
    dateSelected_=document.getElementById("datepicker").value;
  }

  var xhttp;
  var xhttp2;
    // var receivedPeriods=false;
    // var receivedClasses=false;
    var receivedNumFiles=0;
    var targetReceivedNumFiles=2;


  var datepickerValue = dateSelected_;
  var date = new Date(datepickerValue);

  console.log(date)
  
  //for some reason the timestamp has 3 trailing zeroes appended by default - weird
  var str = date.getTime().toString().slice(0,-3);


  xhttp = new XMLHttpRequest(); //request for periods
  xhttp2 = new XMLHttpRequest(); //request for class names
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

        if (this.responseText.includes("[{")){ 

          
            document.getElementById("errorMessage").innerHTML="";

            //there is a leading " that is throughing things off ????????????????????????????!!
            schedule = JSON.parse(this.responseText.substring(1));
            //schedule = this.responseText;
            //console.log("test if statment ready 1" )
            if(receivedNumFiles+1>=targetReceivedNumFiles){ 
            // receivedClasses=false;
            receivedNumFiles=0;

            reInitializeScheduleView();
            }
            else {receivedNumFiles++}
        }
        else {
            // document.getElementById("errorMessage").innerHTML = this.responseText;
        }
    }
  };
    xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        if (this.responseText.includes("[")){ 
            document.getElementById("errorMessage").innerHTML="";
            classes = JSON.parse(this.responseText.substring(1));
            
            if(receivedNumFiles+1>=targetReceivedNumFiles){
                // receivedPerods=false;
              receivedNumFiles=0;


              reInitializeScheduleView();
            }
            else {receivedNumFiles++}
        }
        else {
           //  document.getElementById("errorMessage").innerHTML = this.responseText;
        }
    }
  };
//https://yuhsgschedule.com/testing/respondToDatepicker.php?v=1&timestamp=1539144000


  xhttp.open("GET", "respondToDatepicker.php?v=1&timestamp="+str, true);
  //v=1 - return periods
  xhttp.send('');
//   if(isLoggedIn){

  xhttp2.open("GET", "respondToDatepicker.php?v=2&timestamp="+str, true);
  // }else{
    // targetReceivedNumFiles=1
  // }
  
  //v=2 - return class names
  xhttp2.send(''); 
// }
}*/</script>  
               
<?php 
include_once "common/inc/class.schedule_items.inc.php";
$schedule = new ClassScheduleItems($db);
$valid_days = $schedule->getValidSchoolDays();
$json_valid_days = json_encode($valid_days);
$day_info = $schedule->getSchoolDayInfo(date('Y-m-d',$dateviewing));
if($day_info[1]=='Error'){
    echo "<script> var error_message_onpageload=\"$day_info[0]\";
    window.onload = function(){
    	document.getElementById(\"errorMessage\").innerHTML= error_message_onpageload;
    }
    </script>";
    // include_once("common/footer.php");
    // exit();
}
$class_day = $day_info[0]; 
$bell_schedule = $day_info[1];
$bell_times = $schedule->getBellsNew($bell_schedule);
$json_bells = json_encode($bell_times);
//echo "<pre>".print_r($bell_times,true)."</pre>";
// if(count($bell_times)<2){
//     echo "<h2>Error obtaining times for bell schedule named $bell_schedule.</h2>";
//     include_once("common/footer.php");
//     exit();
// }
if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1){
$class_names = $schedule->getClassesNew($class_day);
//echo "<pre>".print_r($class_names,true)."</pre>";
if(count($class_names)<1){ 
    //echo "<p>Your schedule for this date appears to be empty.</p>";
}
$json_classes = json_encode($class_names);
//echo $json_classes;
echo "<script id=\"scheduleInfo\">var isLoggedIn=true; var classes = $json_classes;";
}
else{
    echo "<script id=\"scheduleInfo\">
    var isLoggedIn=false; var classes=[];";
}
// $dateformat = date('l, F jS', $dateviewing);
// echo "var dateName = \"$dateformat\";";
echo "\nvar validDates = $json_valid_days;\n";
echo "var schedule = $json_bells;</script>";
?>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

<script src = "js/scheduleView0219.js"></script>
<div id="calendarWrapper"></div>
</div>
<?php include_once "common/sidebar.php"; ?>