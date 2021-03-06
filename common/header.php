<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
    <meta http-equiv="refresh" content="43200">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Zilla+Slab">
    <link rel="stylesheet" href="/css/base.css" type="text/css" />
    <title><?php echo $pageTitle ?></title>
   <script src = "js/menu.js"></script>
<link rel="icon" 
      type="image/png" 
      href="favicon.png">
    <!-- Icon courtesy of "schedule" by Markus from the Noun Project -->
    <!-- https://thenounproject.com/hrsaxa/collection/calendar-search-bell-currency-microphone/?i=1776647 -->
     <!--meta name="google-signin-client_id" content="619981127021-appbft15od7mttn34vpsp7eimeuc5k0b.apps.googleusercontent.com"></meta-->
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
<div class="menu">
  <ul>
    <li><a id="home-button" href="index.php"><img src="/icon_ring.png"><span>Home</span></a></li>
    <li><a href="input_schedule.php"><span>Class schedule</span></a></li>
    <li><a href="add_calendar.php"><span>View in Google Calendar</span></a></li>
      <?php if(isset($_SESSION['IsAdmin'])&&$_SESSION['IsAdmin']==1){
      echo "<li><a href = \"admin.php\"><span>Admin panel</span></a></li>";
    } ?>
    <li><a href="logout.php">Log out <?php /*echo "(".$_SESSION['Username'].")";*/?></a></li>
  </ul>
</div>
<?php else:  
    if(SUBDOMAIN!='demo'&&SUBDOMAIN!='testing'){
      $filepath =  explode("/",$_SERVER['SCRIPT_FILENAME']);
      $filename = $filepath[count($filepath)-1];
      if($filename!="gauth.php"){
          echo '<meta http-equiv="refresh" content="0;URL=https://'.SUBDOMAIN.'.classperiods.com/gauth.php">';
        exit();
      }
    }
  
  
  ?>
<div class="menu">
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
