<?php
    include_once "common/base.php";
    $pageTitle = "Reset Your Password";
    include_once "common/header.php";
    if(!empty($_POST['username'])):
        include_once "common/inc/class.users.inc.php";
        $users = new CalendarUsers($db);
        echo $users->resetPassword();
    else:
?>
        <h2>Reset Your Password</h2>
        <p>Enter the email address you signed up with and we'll send
        you a link to reset your password.</p>
 
        <form action="password.php" method="post">
            <div>
                <input type="text" name="username" id="username" />
                <label for="username">Email</label><br /><br />
                <input type="submit" name="reset" id="reset"
                    value="Reset Password" class="button" />
            </div>
        </form>
<?php
    endif;
    include_once "common/close.php";
?>