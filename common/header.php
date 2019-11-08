<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
    <meta http-equiv="refresh" content="43200">
    <link rel="stylesheet" href="style.css" type="text/css" />
    <title> ClassPeriods<?/*php echo $pageTitle*/ ?> </title>
   <link rel="stylesheet" type="text/css" href="style.css">
   <script src = "js/menu.js"></script>
<link rel="icon" 
      type="image/png" 
      href="favicon.png">
    <!-- Icon courtesy of "schedule" by Markus from the Noun Project -->
    <!-- https://thenounproject.com/hrsaxa/collection/calendar-search-bell-currency-microphone/?i=1776647 -->
     <!--meta name="google-signin-client_id" content="619981127021-appbft15od7mttn34vpsp7eimeuc5k0b.apps.googleusercontent.com"></meta-->
     <style>
#menu ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #243d46;
}
#menu li {
    float: left;
}
#menu li a {
    display: block;
    color: white;
    text-align: center;
padding-top:16px;
padding-bottom:16px;
    text-decoration: none;
}
#menu li a:hover {
    background-color: #121523;
}
#nolink {
  cursor:default;
  background-color:#243d46;
}
</style>
</head>
<body>
    <div id="page-wrap">
        
  <div class="header">
<?php
    if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1):
?>
<!-- onclick="toggleClass(theToggle, 'on'); >
<a href="#menu" id="toggle" "><span></span></a-->
<style> #menu li a {padding:16px;}</style>
<div id="menu">
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="input_schedule.php">Class schedule</a></li>
    <li><a href="add_calendar.php">View in Google Calendar</a></li>
      <?php if(isset($_SESSION['IsAdmin'])&&$_SESSION['IsAdmin']==1){
      echo "<li><a href = \"admin.php\">Admin panel</a></li>";
    } ?>
    <li><a href="logout.php">Log out <?php /*echo "(".$_SESSION['Username'].")";*/?></a></li>
  </ul>
</div>
<?php else:  ?>
<div id="menu">
  <ul>
    <li>
      <img src="common/login_icons/signin_normal.png" 
      onmouseenter="this.src='common/login_icons/signin_hover.png';" 
      onmouseleave="this.src='common/login_icons/signin_normal.png';"
      onmousedown="this.src='common/login_icons/signin_pressed.png';"
      onmouseup="this.src='common/login_icons/signin_hover.png';
                location.href='gauth.php';">
    </li>
    <!--li><a href="login.php">&nbsp;Log in&nbsp;</a></li>
    <li><a id="nolink" href="javascript:void(0);" style="background-color: #243d46;">or</a></li>
    <li><a href="signup.php">&nbsp;Sign up&nbsp;</a></li>
    <li><a id ="nolink" href="javascript:void(0);"style ="background-color: #243d46;"> to view your classes</a></li-->
  </ul>
</div>
<?php endif; ?>
            </div>
        </div>
