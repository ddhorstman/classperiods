<?php 
include_once "common/base.php";
$pageTitle = "Class Schedule";
include_once "common/header.php";
include_once "common/inc/class.schedule_items.inc.php";
$schedule = new ClassScheduleItems($db);
    
        if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1){
           $classes = $schedule->getClassSchedule($_SESSION['Username']);
           foreach($classes as $classname){
               str_replace('"', "", $classname);
           }
           $classes_json = json_encode($classes);
         //   print_r($classes);
        }
    else {
        echo "<h2>You must be logged in to view or edit your class schedule.</h2>";
    include_once("common/footer.php");
    exit();
    }
    
    if(!empty($_POST)):
       
    foreach ($_POST as $key=>$val){
      
       //ignore classes whose name didn't change
        if(isset($classes[$key])&&$classes[$key]==$val){
             //echo "<p>Class $val in time slot $key didn't change.</p>";
        }
        //ignore empty cells and the submit_table variable
        else if($val != ""&& $key != "submit_table"){
            $message= $schedule->updateScheduleItem($key,$val);
            //echo $message;
        }
    }
    echo "<p>Schedule Updated Successfully</p>";
    else:
?>
<div id = "main">
<style>
input{
	border-style: none;
	text-align:center;
	background-color: var(--main-color-lighten);
	color:white;
	margin: 0px;
	padding: 0px;
	font-size: 20px;
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
}
</style>
<body>
    <div>  
<!--b><big>Input your class schedule below:</big></b><br-->
<div class="main-content">
<div id="message">
<?php 
if(isset($_GET['new'])&&$_GET['new']==1){
	echo "<strong><big>Enter your classes in the form below:</big></strong><br>";
}
?>
To get started, click on a box and start typing.</div>
<!--div style="color:red;">To delete a class, type "delete" in its timeslot.</div-->
<form method = "post" action = "input_schedule.php" id = "input_schedule" autocomplete="off">
<table id = "classSchedule">
	
	<tr>
		<th>Period</th>
		<th>Monday</th>
		<th>A Day</th>
		<th>B Day</th>
		<th>C Day</th>
		<th>Thursday</th>
		<th>Friday</th>
		</tr>
	
	
	<tr>
	</tr>
		<?php 
		$length = 13; //max length of input
		
		$rows = array(array("",""),array("M01","A01","B01","C01","T01","F01"),
		array("M02","A02","B02","C02","T02","F02"),
		array("M03","A03","B03","C03","T03","F03"),
		array("M04","A04","B04","C04","T04","F04"),
		array("M05","A05","B05","C05","T05","F05"),
		array("M06","A06","B06","C06","T06"),
		array("M07","A07","B07","C07","T07"),
		array("M08","A08","B08","C08","T08"),
		array("M09","A09","B09","C09","T09"),
		array("M10","A10","B10","C10","T10"),
		array("M11","A11","B11","C11","T11"),
		);
		$rows_json= json_encode($rows);
		
		for($j=1;$j<count($rows);$j++){
		$row = $rows[$j];
		echo "<tr><th id = \"period\">$j</th>";
		for($i=0;$i<count($row);$i++){
		  echo "<td id=\"$row[$i]Cell\"";
		  if(isset($classes[$row[$i]])){
		  	echo "style = \"background-color: var(--main-color-unselected);\"";
		  }
		  echo "><input type=\"text\" oninput=\"setColorActive(this.id)\" onblur=\"updateClass(this.id,this.value)\" maxlength=\"$length\" size = \"".($length-3)."\" autocomplete= \"school-class\" name=\"$row[$i]\" id=\"$row[$i]\"";
		    if(isset($classes[$row[$i]])){
		        //remove quotation marks to prevent html code injection
		        $holdClassName = str_replace("\"","",$classes[$row[$i]]);
		        echo "value = \"$holdClassName\"";
		        echo "style = \"background-color: var(--main-color-unselected);\"";
		    }
		    // else if($row[$i]=="T10"){
		    // 	echo "value = \"Plus Period\"";
		    // 	echo "style = \"background-color: var(--filled-color);\"";
		    // }
		    echo "></td>";
		}
		echo "</tr>";
		}
		?>
		 <!--td><input type="text" name="M01" id="M01"></td>
		 <td><input type="text" name="A01" id="A01"></td>
		 <td><input type="text" name="B01" id="B01"></td>
		 <td><input type="text" name="C01" id="C01"></td>
		 <td><input type="text" name="T01" id="T01"></td>
		 <td><input type="text" name="F01" id="F01"></td>
	</tr-->
	
	
</table>
<script>
		<?php echo "var tableIndex=$rows_json; var classesCurrent=$classes_json;"; ?> 
			var messageShown = false;
			var hasUsedDeleteBefore = false;
			function setColorActive(Period){
				Name = document.getElementById(Period.toString()).value.toString();
			    if(Name.toString()==''){
			       document.getElementById(Period.toString()).style.background="var(--main-color-lighten)";
			 document.getElementById(Period.toString()+"Cell").style.background="var(--main-color-lighten)"; 
			    }
			    else{
			 document.getElementById(Period.toString()).style.background="var(--main-color-unselected)";
			 document.getElementById(Period.toString()+"Cell").style.background="var(--main-color-unselected)";
			    }
			}
			function updateClass(Period,Name){
				if(Name.toString()!=""){
					classesCurrent[Period]=Name;
				}
			if(Name.toString()==""&&typeof classesCurrent[Period]!=='undefined'){
				Name="delete";
			}
			if(Name.toString()!=""){
				document.getElementById("message").innerHTML="Changes pending...";
				var http = new XMLHttpRequest();
				var url = 'processClassInput.php';
				var params = 'c='+encodeURIComponent(Name.toString())+'&p='+encodeURIComponent(Period.toString());
				http.open('POST',url,true);
				http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				http.onreadystatechange = function() {//Call a function when the state changes.
				    if(http.readyState == 4 && http.status == 200) {
				        document.getElementById("message").innerHTML="Schedule updated!";
			       document.getElementById(Period.toString()).style.background="var(--main-color)";
			 		document.getElementById(Period.toString()+"Cell").style.background="var(--main-color)"; 
				        // document.getElementById(Period.toString()).style.background="#121523";
				        // document.getElementById(Period.toString()+"Cell").style.background="#121523";
				        if(this.responseText.toString().includes("Delete")){
				        	hasUsedDeleteBefore=true;
				        	document.getElementById(Period.toString()).value="";
				        	document.getElementById(Period.toString()).style.background="var(--main-color-lighten-more)";
				        	document.getElementById(Period.toString()+"Cell").style.background="var(--main-color-lighten-more)";
				        }
				    }
				}
				http.send(params);
			//document.getElementById("message").innerHTML=Name.toString()+" Saved Successfully";
			if(!messageShown){setTimeout(resetMessage,1000);}
			setTimeout(setColorActive.bind(Period,Period),1000);
		}
		function resetMessage(){
			document.getElementById("message").innerHTML="All changes will save automatically.";
			if(!hasUsedDeleteBefore){
				document.getElementById("message").innerHTML += " To remove a class, just delete its name.";
			}
			messageShown=false;
		}
		}
	</script>
<!--input type="submit" name = "submit_table" id = "submit_table" value = "Submit"/-->
</form>
	</div>
	</div>
	<?php 
	    endif;
	include_once "common/sidebar.php"; ?>
<?php include_once "common/footer.php"; ?>