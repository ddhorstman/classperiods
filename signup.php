<?php
    include_once "common/base.php";
    $pageTitle = "Register";
    include_once "common/header.php"; ?>
    <style>
    #main-signup {padding-left: 16px;}
    h2 + p {
        margin-top:-20px;
    }
</style><div id ="main-signup">
    <?php    
        //redirect signed in users to the homepage
        if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1){
          echo "<meta http-equiv='refresh' content='0;index.php'>";
            include_once 'common/footer.php';
            exit();
        }
    if(!empty($_POST['username'])):
        include_once "common/inc/class.users.inc.php";
        $users = new CalendarUsers($db);
        echo $users->createAccount();
    else:
?>
    <style>input[type="text"], input[type="password"] { display: block; } </style>
        <h2>Sign up</h2>
        <p>Signing up allows you to save and view your personal class schedule.</p>
        <form method="post" action="signup.php" id="registerform">
            <div>
                <label for="username">Email:</label>
                <input type="text" name="username" id="username" />
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" />
                <label for="password-confirm">Confirm Password:</label>
                <input type="password" name="password-confirm" id="password-confirm" />
                <input type="submit" name="register" id="register" value="Sign up" />
            </div>
        </form>
 </div>
<?php
    endif;
include_once 'common/footer.php';
  include_once 'common/close.php';
?>