<?php
   include_once "common/base.php";
   $pageTitle = "Add Calendar";
    include_once "common/header.php";
    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])):
                include_once 'common/inc/class.users.inc.php';
                $users = new CalendarUsers($db);
                $extID= $users->retrieveExtID();
       // $extID=$_SESSION['Username'];
?>
   <style>
   h3 + ol {
    margin-top:-10px;
   }
.tooltip {
    position: relative;
    display: inline-block;
}
.tooltip .tooltiptext {
    visibility: hidden;
    width: 140px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 150%;
    left: 50%;
    margin-left: -75px;
    opacity: 0;
    transition: opacity 0.3s;
}
.tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}
.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}
</style>
<script>
function copyLink() {
  var copyText = document.getElementById("calendarURL");
  copyText.select();
  document.execCommand("copy");
  
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Link copied\!";
}
function outFunc() {
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Copy to clipboard";
}
</script>
 
        <!--big><b>Follow the directions below to add your classes to Google Calendar:</big></b><br-->
<div style="color:red;">
        Note: Your calendar will update automatically, but it may take up to 48 hours for changes to appear in Google Calendar.
</div>
      <h3>Instructions:</h3>
        <ol id = "calendarInstructions">
            <li>Copy this calendar link to your clipboard: 
                <input onfocus="this.select();" type="text" value="<?php 
                echo "https://".$_SERVER['HTTP_HOST']."/calendar.php?extID=".$extID; 
                ?>" id="calendarURL">
                    <div class="tooltip">
                    <button onclick="copyLink()" onmouseout="outFunc()">
                     <span class="tooltiptext" id="myTooltip">Copy to clipboard</span>
                     Copy link
                     </button>
                    </div></li>
            <li>Go to the "<a href="https://calendar.google.com/calendar/r/settings/addbyurl" target="_blank">Add calendar by URL</a>" page in Google Calendar.</li>
            <li>Find the "URL of calendar" box and paste in the link.</li>
            <li>Click on "Add calendar".</li>
            <li>You're done! You can change the name by clicking on "Class Schedule" in the sidebar.</li>
            
        </ol>
<?php
    else:
?>
 
       <meta http-equiv="refresh" content="0;URL='/'" /> 
<?php
    endif;
?>
 
        
<?php
    include_once "common/footer.php";
    include_once "common/close.php";
?>