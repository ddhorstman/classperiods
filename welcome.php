<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
    <link rel="icon" 
      type="image/png" 
      href="favicon.png">
    <title>ClassPeriods | Welcome</title>
</head>
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
    alert("Hello");
}
</script>

<body>
Splash screen in development!<br>
You can view the <a href="https://yuhsg.classperiods.com">YUHSG</a> schedule page.
</body>
</html>