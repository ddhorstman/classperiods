<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <link rel="icon" 
      type="image/png" 
      href="favicon.png">
    <title>ClassPeriods | Welcome</title>
</head>
<style>
    a {
        text-decoration:none;
        color: inherit;
    }
    #school-list{
        display:flex;
        flex-flow: row wrap;
        justify-content: space-evenly;
        text-align: center;
    }
    #school-list a {
        width: 380px;
        border: 2px solid blue;
        border-radius: 10px;
        display:flex;
        flex-flow: row nowrap;
        justify-content: space-evenly;
        align-items: center;
    }
    #school-list a img {
        padding: 10px;
        width:48%;
    }
    #school-list a h3{
        padding:10px;
        width:48%;
    }

</style>
<script>
<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('default_charset','utf-8');

// Start a PHP session
session_start();
$_SESSION['date_offset']=0;
// Include site constants
include_once "common/inc/constants.inc.php";

// Create a database object
try {
    $dsn = "mysql:host=".DB_HOST.";dbname="."participating-schools-info";
    $db = new PDO($dsn, DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo "Database Error.";
    exit();
}

    $sql = "SELECT FullName, SubdomainName, LogoURL
            FROM list_of_schools";
    try{
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = array();
        while($row = $stmt->fetch()){
            if(isset($row["FullName"])&&$row["FullName"]!=null){
                $row1 = array();
                $row1["name"]=$row["FullName"];
                $row1["url"]="https://".$row["SubdomainName"].".classperiods.com";
                $row1["image"]=$row["LogoURL"];
            }
            $result[] = $row1; 
        }
        $stmt->closeCursor();
        $schools = json_encode($result);
    }
    catch(PDOException $e){
        die($e->getMessage());
    }

echo "var participatingSchools = ".$schools.";\n";
?>
window.onload = function(){
    var section = document.getElementById("school-list");
    for(let i = 0; i < participatingSchools.length; i++){
        var currentSchool = participatingSchools[i];
        var schoolDiv = document.createElement("a");
        schoolDiv.href=currentSchool.url;
        var logo = document.createElement("img");
        logo.src = currentSchool.image;
        schoolDiv.appendChild(logo);
        var schoolName = document.createElement("h3");
        var name = document.createTextNode(currentSchool.name);
        schoolName.appendChild(name);
        schoolDiv.appendChild(schoolName);

        section.appendChild(schoolDiv);
    }
}
</script>
<body>
<header>
    <h1>ClassPeriods</h1>
    <p>ClassPeriods is a schedule management app that makes it easy for students
       and faculty to keep track of their schedules. It supports rotating and block
       schedules, half days, custom event schedules, and more!
    </p>
</header>
<section class = "information">
    <h2>Particpating schools</h2>
    <p>If your school has signed up, you can find a link to your school's schedule
       page below. If you want to sign up for the service, contact
       <a href="mailto:sales@classperiods.com">sales@classperiods.com</a>
       for more information.</p>
</section>
<section id="school-list">
</section>
</body>
</html>