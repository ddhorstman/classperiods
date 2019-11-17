<?php 
include_once "common/base.php";
$pageTitle = "Admin Panel";
include_once "common/header.php";
include_once "common/inc/class.school_calendar.inc.php";

if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
	&& $_SESSION['LoggedIn']==1){
	$schoolCal = new SchoolCalendar($db);
$valid = $schoolCal->validateAdmin();
       //echo "<h2>It is $valid</h2>";
	if(!$valid){
		echo "<h2>You must an administrator to edit the School Calendar.</h2>"
		."<p>Contact <a href = \"mailto:help@classperiods.com\">help@classperiods.com</a>"
		." if you believe this is in error.</p>";
		include_once("common/footer.php");
		exit();
	}
	else{
		$currentMonth=date('m');
		echo "<script>var calendarData= ";
		echo json_encode($schoolCal->getvalidSchoolDays($currentMonth));
		echo ";\n";
		echo "var classDays = ";
		echo $schoolCal->getDayNames();
		echo ";\n";
		echo "var bellSchedules = ";
		echo json_encode($schoolCal->getBellScheduleNames());
		echo "; </script>\n";
	}
} 
else {
	echo "<meta http-equiv='refresh' content='0;index.php'>";
	include_once("common/footer.php");
	exit();
}

?>
<div class="main-content">
<h3>School Calendar</h3>
<div id = "calendarWrapper">
	<div id = "calendarMonth">Loading...</div>
	<!--div id = "loadingMessage">Loading calendar...</div-->
	<table id="calendarView"></table></div>
	<div id = "statusMessage"></div>
</div>
</div>
<script>
	var date = new Date();
	var monthOffset = 0;
	var monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
	var dayNames = ["Sun","Monday","Tuesday","Wednesday","Thursday","Friday","Sat"];
	// var classDays = [["delete","No Classes"],["M","Monday"],["A","A Day"],["B","B Day"],["C","C Day"],["T","Thursday"],["F","Friday"]];
	//var bellSchedules = [["delete","No School"],["Regular","Regular (M-Th)"],["Friday","Friday"],["Early Dismissal","Early Dism (4:40)"],["Fast Day","Fast Day (1:45)"],["AM Assembly","AM Assembly"],["PM Assembly","PM Assembly"]];
	var classDayNames = "";
	var bellScheduleNames = "";
	window.onload = function() {
		date = new Date(date.getFullYear(),date.getMonth()+monthOffset);
		buildCalendar(date.getFullYear(),date.getMonth());
	}
	function prevDate(){
		//document.getElementById('calendarMonth').innerHTML="Loading...";
		document.getElementById('calendarView').innerHTML="";
		monthOffset-=1;
		date = new Date(date.getFullYear(),date.getMonth()-1);
		getDaysforNewMonth(date.getMonth()+1);
		//buildCalendar(date.getFullYear(),date.getMonth());
	}
	function nextDate(){
		//document.getElementById('calendarMonth').innerHTML="Loading...";
		document.getElementById('calendarView').innerHTML="";
		monthOffset+=1;
		date = new Date(date.getFullYear(),date.getMonth()+1);
		getDaysforNewMonth(date.getMonth()+1);
		//buildCalendar(date.getFullYear(),date.getMonth());
	}
function getDaysforNewMonth(_month){
	var xhttp;
	var identifier = "caldata=";
	var receivedData = "";
	xhttp = new XMLHttpRequest();
	  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    	receivedData=this.responseText;
    	receivedData=receivedData.substring(receivedData.indexOf(identifier)+identifier.length);
    	//document.getElementById("statusMessage").innerHTML=receivedData;
    	calendarData=JSON.parse(receivedData);
    	buildCalendar(date.getFullYear(),date.getMonth());
    }
  };
    xhttp.open("GET", "admin_backend.php?r=0&m="+_month, true);
  //v=1 - return periods
  xhttp.send('');
}

function updateSchoolDay(_date,_classes,_bells,_cell){
	var xhttp;
	var identifier = "schoolDayUpdate=";
	var receivedData = "";
	xhttp = new XMLHttpRequest();
	  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    	receivedData=this.responseText;
    	receivedData=receivedData.substring(receivedData.indexOf(identifier)+identifier.length);
    	document.getElementById("statusMessage").innerHTML=receivedData;
    	if(_bells==="delete"){
    	_cell.style.background="var(--main-color-lighten-more)";
    	}
    	else {
    	_cell.style.background="var(--main-color)";
    	}
    	setTimeout(function(){ document.getElementById("statusMessage").innerHTML="";},3000);
    	if(this.responseText.includes("elete")){
    		setTimeout(function(){_cell.style.background="var(--main-color-lighten)";},1000);
    	}
    	else{
    		setTimeout(function(){_cell.style.background="var(--main-color-unselected)";},1000);
    	}
    }
  };
    xhttp.open("GET", "admin_backend.php?r=1&d="+_date+"&c="+_classes+"&b="+_bells, true);
  //v=1 - return periods
  xhttp.send('');
}

function prepareToUpdateSchoolDay(_sender,_type){
	var parent = _sender.parentNode;
	var children = parent.childNodes;
	var date = parent.id;
	var classes = ""; var bells = "";
	//retrieve the data from the select you just changed
	var selection = _sender.options[_sender.selectedIndex].value;
	if(_type==='classes'){
		classes = selection;
		if(classes==="delete"){
			//if you're deleting, set the other parameter to delete and make the select red with "true"
			setCellData(parent,'bellScheduleSelect',"delete");
		}
		for(var i = 0; i < children.length; i++){
			if(children[i].className==='bellScheduleSelect'){
				bells = children[i].options[children[i].selectedIndex].value;
				break;
			}
		}
	}
	else if(_type==='bells'){
		bells = selection;
		if(bells==="delete"){
			//if you're deleting, set the other parameter to delete and make the select red with "true"
			setCellData(parent,'classDaySelect',"delete");
		}
		for(var i = 0; i < children.length; i++){
			if(children[i].className==='classDaySelect'){
				classes = children[i].options[children[i].selectedIndex].value;
				break;
			}
		}
	}
	if(classes==="delete"&&bells!=="delete"){
		setCellData(parent,'classDaySelect',"","2px solid red");
		document.getElementById("statusMessage").innerHTML="<div style=\"color:red;\">Finish editing that school day to save.</div>";
	}
	else if(classes!=="delete"&&bells==="delete"){
		setCellData(parent,'bellScheduleSelect',"","2px solid red");
		document.getElementById("statusMessage").innerHTML="<div style=\"color:red;\">Finish editing that school day to save.</div>";
	}
	else {
		setCellData(parent,'classDaySelect',"","none");
		setCellData(parent,'bellScheduleSelect',"","none");
		updateSchoolDay(date,classes,bells,parent);
	}
	//document.getElementById("statusMessage").innerHTML="Date is "+date+", classes are "+classes+", and bells are "+bells;
}

function buildCalendar(year,month){

	//document.getElementById("loadingMessage").innerHTML="";
	document.getElementById('calendarMonth').innerHTML="<button style=\"padding: 0;border: none;background: none;\" onclick=\"prevDate()\"><img style=\"width: 15px;height: 20px;\" src=\"js/assets/arrow_left.png\" alt=\"←\"></button><span>"+
	monthNames[month]+" "+year+
	"</span class=\"admin-span\"><button style=\"padding: 0;border: none;background: none;\" onclick=\"nextDate()\"><img style=\"width: 15px;height: 20px;\" src=\"js/assets/arrow_right.png\" alt=\"→\"></button>";
	var calendarView = document.getElementById('calendarView');
	calendarView.innerHTML="";
	var daysInMonth = new Date(year, month+1, 0).getDate();
	classDayNames="";
	for(var i = 0; i <classDays.length; i++){
		classDayNames+="<option value=\""+classDays[i][0]+"\">"+classDays[i][1]+"</option>";
	}
	bellScheduleNames="";
	for(var i = 0; i <bellSchedules.length; i++){
		bellScheduleNames+="<option value=\""+bellSchedules[i][0]+"\">"+bellSchedules[i][1]+"</option>";
	}


	calendarView.insertRow(0);
	for(var i = 0; i <dayNames.length; i++){
		calendarView.rows[0].insertCell(i);
		calendarView.rows[0].cells[i].outerHTML="<th>"+dayNames[i]+"</th>";
	}
	var rowNumber = 1;
	//add a new empty row
	addEmptyRow(calendarView,rowNumber);
	for(var i = 1; i <= daysInMonth; i++){
		var currentDate = new Date(year,month,i);
		var dayNumber = currentDate.getDay();
		//if it's Sunday, start a new row
		if(dayNumber===0&&i!==1){
			rowNumber++;
			addEmptyRow(calendarView,rowNumber);
		}

		//if it's a weekday, fill the cell
		if(1<=dayNumber&&dayNumber<=5){
			var cellContents = "<big>"+i+"</big><br>Classes:<select onchange=\"prepareToUpdateSchoolDay(this,\'classes\');\" class=\"classDaySelect\">"+classDayNames+"</select><br>Bells:<select onchange=\"prepareToUpdateSchoolDay(this,\'bells\');\" class=\"bellScheduleSelect\">"+bellScheduleNames+"</select>";
			calendarView.rows[rowNumber].cells[dayNumber].innerHTML=cellContents;
			//var dateString = currentDate.toDateString().substring(4);
			var dateString = currentDate.getFullYear()+"-";
			if(currentDate.getMonth()+1<10)dateString+="0";
			dateString+=(currentDate.getMonth()+1)+"-";
			if(currentDate.getDate()<10)dateString+="0";
			dateString+=currentDate.getDate();
			calendarView.rows[rowNumber].cells[dayNumber].setAttribute("id",dateString);
		}
		else {
			calendarView.rows[rowNumber].cells[dayNumber].innerHTML="<big>"+i+"</big>";
		}
		//pre-set fixed weekday values
		var targetCell = calendarView.rows[rowNumber].cells[dayNumber];
		if(dayNumber==1){
			setCellData(targetCell,'classDaySelect','M');
		}
		if(dayNumber==4){
			setCellData(targetCell,'classDaySelect','T');
		}
		if(dayNumber==5){
			setCellData(targetCell,'classDaySelect','F');
		}

	}
	//load data from database into calendar cells
	for(var g = 0; g < calendarData.length; g++){
		var targetCell = document.getElementById(calendarData[g]["Date"]);
		if(targetCell!=null){
			setCellData(targetCell,'classDaySelect',calendarData[g]["Classes"]);
			setCellData(targetCell,'bellScheduleSelect',calendarData[g]["Bells"]);
			targetCell.setAttribute("style","background-color:var(--main-color-unselected);")
		}
	}
}
function setCellData(_targetCell,_targetData,_dataValue, _changeColor=0){
	var children = _targetCell.childNodes;
			//console.log(children);
	for(var k= 0; k < children.length; k++){
		if(children[k].className===_targetData){
			var targetDataSelector = children[k];
			if(_changeColor!==0){
				targetDataSelector.style.outline=_changeColor;
			}
			else{
				for(var m = 0; m < targetDataSelector.options.length; m++){
					if (targetDataSelector.options[m].value===_dataValue){
						targetDataSelector.options[m].selected=true;
						break;
					}
				}
			}
		}
	}

}
		function addEmptyRow(calendarView,rowNumber){
			calendarView.insertRow(rowNumber);
			for(var j = 0; j <dayNames.length; j++){
				calendarView.rows[rowNumber].insertCell(j);
			}
		}
</script>
<style>
	.admin-span{
		display: inline-block;
		text-align: center;
		width: 150px;
	}
	input{
		border-style: none;
		text-align:center;
		vertical-align: top;
		background-color: var(--main-color-lighten);
		color:white;
		font-size 1.1em/1.4;
	}
	table, th, td {
		color:white;
		border: 2px solid white;
		border-collapse: collapse;

	}
	th{
		background-color: var(--header-color);
		/*background-color: #121523;*/
	}
	td {
		background-color: var(--main-color-lighten);
		padding: 5px;
		text-align: right;
		vertical-align: top;
	}
</style>
<?php 
include_once "common/sidebar.php";
include_once "common/footer.php"; ?>