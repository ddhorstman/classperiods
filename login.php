<?php
   include_once "common/base.php";
   $pageTitle = "Login";
    include_once "common/header.php"; ?>
<style>
    #main-login {padding-left: 16px;}
    input[type="text"], input[type="password"] { display: block; }
</style><div id ="main-login">
 <?php
    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])):
?>
 
        <p>You are currently <strong>logged in.</strong></p>
        <p><a href="logout.php">Log out</a></p>
<?php
    elseif(!empty($_POST['username']) && !empty($_POST['password'])):
        include_once 'common/inc/class.users.inc.php';
        $users = new CalendarUsers($db);
        if($users->accountLogin()===TRUE):
          echo "<meta http-equiv='refresh' content='0;index.php'>";
            exit();
        else:
?>
 
        <h2>Login Failed&mdash;Try Again?</h2>
        <form method="post" action="login.php" name="loginform" id="loginform">
            <div>
                <label for="username">Email:</label>
                <input type="text" name="username" id="username" />
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" />
                <input type="submit" name="login" id="login" value="Login" class="button" />
            </div>
        </form>
        <p>Did you want to <a href="signup.php">sign up for an account</a> instead?</p>
<?php
        endif;
    else:
?>
 
        <h2>Log in to YUHSG Schedule</h2>
        <form method="post" action="login.php" name="loginform" id="loginform">
            <div>
                <label for="username">Email:</label>
                <input type="text" name="username" id="username" />
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" />
                <input type="submit" name="login" id="login" value="Login" class="button" />
            </div>
        </form><br /><br />
       <!-- <p><a href="/password.php">Did you forget your password?</a></p> -->
<?php
    endif;
?>
 
        </div><div style="clear: both;"></div>
<?php
    include_once "common/close.php";
?>