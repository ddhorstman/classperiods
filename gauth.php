<?php
include_once "common/base.php";
$pageTitle = "Google Authentication Redirect";
include_once "common/header.php"; ?>

<?php
require_once 'vendor/autoload.php';
 
// init configuration
$clientID = '941100738123-9p7co0u26h9o28ab8ft3e6is4dqjij21.apps.googleusercontent.com';
$clientSecret = 'qK5XDU5KKBSlZe8z8tkQz_fp';
$redirectUri = 'https://yuhsgschedule.com/gauth.php';
  
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
 
// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);
  
  // get profile info
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $email =  $google_account_info->email;
  $name =  $google_account_info->name;
  include_once 'common/inc/class.users.inc.php';
  $users = new CalendarUsers($db);
  //echo $email;
  echo $users->googleLogin($email);
   //echo '<meta http-equiv="refresh" content="0;url=input_schedule.php" >';
 
  // now you can use this profile info to create account in your website and make user logged in.
} else {
  echo '<meta http-equiv="refresh" content="0;url='
	.$client->createAuthUrl().'" >';
}
?>

<?php

    include_once "common/sidebar.php";

    include_once "common/footer.php";

?>