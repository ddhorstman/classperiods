<?php

/**
 * Handles user interactions within the app
 *
 * PHP version 5
 *
 * @author Jason Lengstorf
 * @author Chris Coyier
 * @copyright 2009 Chris Coyier and Jason Lengstorf
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 */
class CalendarUsers
{

  /**
     * The database object
     *
     * @var object
     */
  private $_db;

    /**
     * Checks for a database object and creates one if none is found
     *
     * @param object $db
     * @return void
     */
    public function __construct($db=NULL)
    {
        if(is_object($db))
        {
            $this->_db = $db;
        }
        else
        {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
    }
    
    public function googleLogin($email){

        $sql = "SELECT Username, IsAdmin
        FROM users
        WHERE Username=:user
        LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $email, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1)
            {
                $_SESSION['Username'] = htmlentities($email, ENT_QUOTES);
                $_SESSION['LoggedIn'] = 1;
                $row = $stmt->fetch();
                $_SESSION['IsAdmin']=$row['IsAdmin'];
                //log in existing user and redirect to homepage
                return '<meta http-equiv="refresh" content="0;url=index.php" >';
            }
            else
            {
               $sql = "INSERT INTO users(Username, ExtID)
               VALUES(:email, UUID())";
               if($stmt = $this->_db->prepare($sql)) {
                $stmt->bindParam(":email", $email, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
                $_SESSION['Username'] = htmlentities($email, ENT_QUOTES);
                $_SESSION['LoggedIn'] = 1;
                $_SESSION['isAdmin'] = 0;

                //log in new user and redirect to class schedule
                return '<meta http-equiv="refresh" content="0;url=input_schedule.php?new=1" >';
            }
        }
    }
        catch(PDOException $e)
        {
            return "database error";
        }
        
        return $email;

    }


    public function createAccount()
    {
        $u = trim($_POST['username']);
        $p = trim($_POST['password']);
        $p2 = trim($_POST['password-confirm']);
        $v = sha1(time());
        // if(substr($u,-10)!="@yuhsg.org"||$u=="@yuhsg.org"){
        //                 return "<h2>Error</h2>
        //     <p>Invalid email address. Please <a href='signup.php'>try again</a> with your YUHSG email.</p>";
        // }
        if($p==""){
            return "<h2>Error</h2>
            <p>You must set a password. Please <a href='signup.php'>try again.</a></p>";
            
        }
        if($p!=$p2){
            return "<h2>Error</h2>
            <p>Passwords don't match. Please <a href='signup.php'>try again.</a></p>";
            
        }

        $sql = "SELECT COUNT(Username) AS theCount
        FROM users
        WHERE Username=:email";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $u, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if($row['theCount']!=0) {
                return "<h2> Error </h2>"
                . "<p> Sorry, that email is already in use. "
                . "Do you want to <a href = 'login.php'>Login?</a>";
            }
          /*
            if(!$this->sendVerificationEmail($u, $v)) {
                return "<h2> Error </h2>"
                    . "<p> There was an error sending your"
                    . " verification email. Please "
                    . "contact me at david@horstman.tk"
                    . "for support. We apologize for the "
                    . "inconvenience. </p>";
            }
            */
            $stmt->closeCursor();
        }
        $code = 1;
        $sql = "INSERT INTO users(Username, Password, ver_code, verified, ExtID)
        VALUES(:email, MD5(:pass), :ver, :isver, UUID())";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":email", $u, PDO::PARAM_STR);
            $stmt->bindParam(":pass", $p, PDO::PARAM_STR);
            $stmt->bindParam(":ver", $v, PDO::PARAM_STR);
            $stmt->bindParam(":isver", $code, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $userID = $this->_db->lastInsertId();
            $url = dechex($userID);

            /*
             * If the UserID was successfully
             * retrieved, create a default list.
             */
            $sql = "INSERT INTO schedules (UserID)
            VALUES ($userID)";
            if(!$this->_db->query($sql)) {
                return "<h2> Error </h2>"
                . "<p> Your account was created, but "
                . "creating your first list failed. </p>";
            } else {
                $loginreturn = $this->accountLogin();
                return "<meta http-equiv='refresh' content='2;input_schedule.php?new=1'>"
                ."<h2> Success! </h2>"
                . "<p> Your account was successfully "
                . "created with the username <strong>$u</strong>."
                . "<br><a href=\"input_schedule.php?new=1\">Click here</a> to start adding your classes.";
            }
        } else {
            return "<h2> Error </h2><p> Couldn't insert the "
            . "user information into the database. </p>";
        }
    }
/**
     * Sends an email to a user with a link to verify their new account
     *
     * @param string $email    The user's email address
     * @param string $ver    The random verification code for the user
     * @return boolean        TRUE on successful send and FALSE on failure
     */

private function sendVerificationEmail($email, $ver)
{
  {
        $e = sha1($email); // For verification purposes
        $to = trim($email);

        $subject = "[Central Cal Creator] Please Verify Your Account";

        $headers = "From: Central Cal Creator <noreply@000webhost.io>\r\n";
        $headers .= "Reply-To: Central Cal Creator <noreply@000webhost.io>\r\n";
        $headers .= "Organization: Central Cal Creator\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;

        $msg = "
        You have a new account at Central Cal Creator!

        To get started, please activate your account and choose a
        password by following the link below.

        Your Username: $email

        Activate your account: http://calcreator.horstman.tk/accountverify.php?v=$ver&e=$e

        If you have any questions, please contact david@horstman.tk.

        --
        Thanks!

        David Horstman
        calcreator.horstman.tk";

        return mail($to, $subject, $msg, $headers);
    } 
}
private function sendResetEmail($email, $ver)
{
  {
        $e = sha1($email); // For verification purposes
        $to = trim($email);

        $subject = "[Central Cal Creator] Password Reset Request";
        $msg = "You've requested a password reset for your account!

        Your Username: $email

        Follow this link to choose a new password: http://calcreator.horstman.tk/accountverify.php?v=$ver&e=$e

        If you have any questions, please contact david@horstman.tk.

        --
        Thanks!

        David Horstman
        calcreator.horstman.tk";

        $headers = "From: Central Cal Creator <noreply@000webhost.io>\r\n";
        $headers .= "Reply-To: Central Cal Creator <noreply@000webhost.io>\r\n";
        $headers .= "Organization: Central Cal Creator\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;

        return mail($to, $subject, $msg, $headers);
    } 
}
public function accountLogin()
{
    $sql = "SELECT Username, IsAdmin
    FROM users
    WHERE Username=:user
    AND Password=MD5(:pass)
    LIMIT 1";
    try
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':user', $_POST['username'], PDO::PARAM_STR);
        $stmt->bindParam(':pass', $_POST['password'], PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()==1)
        {
            $_SESSION['Username'] = htmlentities($_POST['username'], ENT_QUOTES);
            $_SESSION['LoggedIn'] = 1;
            $row = $stmt->fetch();
            $_SESSION['IsAdmin']=$row['IsAdmin'];
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    catch(PDOException $e)
    {
        return FALSE;
    }
}
public function retrieveAccountInfo()
{
    $sql = "SELECT UserID
    FROM users
    WHERE Username=:user";
    try
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return $row['UserID'];
    }
    catch(PDOException $e)
    {
        return FALSE;
    }
}

public function retrieveExtID()
{
    $sql = "SELECT ExtID
    FROM users
    WHERE Username=:user";
    try
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        $stmt->closeCursor();
        return $row['ExtID'];
    }
    catch(PDOException $e)
    {
        return "";
    }
}
public function deleteAccount(){
    if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1)
    {
            // Delete schedule items
        $sql = "DELETE FROM schedule_items
        WHERE UserID=(
        SELECT UserID
        FROM users
        WHERE Username=:email
        LIMIT 1
    )";
    try
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":email", $_SESSION['Username'], PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        die($e->getMessage());
    }

            // Delete the user's schedule
    $sql = "DELETE FROM schedules
    WHERE UserID=:user";
    try
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":user", $_POST['user-id'], PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        die($e->getMessage());
    }

            // Delete the user
    $sql = "DELETE FROM users
    WHERE UserID=:user
    AND Username=:email";
    try
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":user", $_POST['user-id'], PDO::PARAM_INT);
        $stmt->bindParam(":email", $_SESSION['Username'], PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    catch(PDOException $e)
    {
        die($e->getMessage());
    }

            // Destroy the user's session and send to a confirmation page
    unset($_SESSION['LoggedIn'], $_SESSION['Username']);
    header("Location: ../gone.php");
    exit;
}
else
{
    echo "<h1>Error</h1>";
}
}
public function verifyAccount()
{
    $sql = "SELECT Username
    FROM users
    WHERE ver_code=:ver
    AND SHA1(Username)=:user
    AND verified=0";

    if($stmt = $this->_db->prepare($sql))
    {
        $stmt->bindParam(':ver', $_GET['v'], PDO::PARAM_STR);
        $stmt->bindParam(':user', $_GET['e'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        if(isset($row['Username']))
        {
                // Logs the user in if verification is successful
            $_SESSION['Username'] = $row['Username'];
            $_SESSION['LoggedIn'] = 1;
        }
        else
        {
            return array(4, "<h2>Verification Error</h2>"
                . "<p>This account has already been verified. "
                . "Did you forget "
                . "your password?</a>");
        }
        $stmt->closeCursor();

            // No error message is required if verification is successful
        return array(0, NULL);
    }
    else
    {
        return array(2, "<h2>Error</h2>n<p>Database error.</p>");
    }
}
public function resetPassword(){
    if(isset($_POST['username'])){
     $u = trim($_POST['username']);
     $v = sha1(time());

     $sql = "SELECT COUNT(Username) AS theCount
     FROM users
     WHERE Username=:email";
     if($stmt = $this->_db->prepare($sql)) {
        $stmt->bindParam(":email", $u, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row['theCount']==0) {
            return "<h2> Error </h2>"
            . "<p> Sorry, there is no account associated with that email address. "
            . "Please try again. </p>";
        }
        if(!$this->sendResetEmail($u, $v)) {
            return "<h2> Error </h2>"
            . "<p> There was an error sending your"
            . " verification email. Please "
            . "contact me at david@horstman.tk"
            . "for support. We apologize for the "
            . "inconvenience. </p>";
        }
        $stmt->closeCursor();
    }

    $sql = "UPDATE users
    SET ver_code=:ver, verified=0
    WHERE Username=:user
    LIMIT 1";
    try
    {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":user", $_POST['username'], PDO::PARAM_STR);
        $stmt->bindParam(":ver", $v, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        return "<h2> Success! </h2>"
        . "<p> A password reset link was sent to"
        . "the email address associated with your account: <strong>$u</strong>."
        . " Check your email!  Note that messages are sometimes erroneously marked as spam.";
    }
    catch(PDOException $e)
    {
        return FALSE;
    }
}


}
public function updatePassword()
{
    if(isset($_POST['p'])
        && isset($_POST['r'])
        && $_POST['p']==$_POST['r'])
    {
        $sql = "UPDATE users
        SET Password=MD5(:pass), verified=1
        WHERE ver_code=:ver
        LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":pass", $_POST['p'], PDO::PARAM_STR);
            $stmt->bindParam(":ver", $_POST['v'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            return TRUE;
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
    else
    {
        return FALSE;
    }
}
    /*
 public function updateEmail()
    {
        $sql = "UPDATE users
                SET Username=:email
                WHERE UserID=:user
                LIMIT 1";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':email', $_POST['username'], PDO::PARAM_STR);
            $stmt->bindParam(':user', $_POST['userid'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
 
            // Updates the session variable
            $_SESSION['Username'] = htmlentities($_POST['username'], ENT_QUOTES);
 
            return TRUE;
        }
        catch(PDOException $e)
        {
            return FALSE;
        }
    }
*/
}


?>