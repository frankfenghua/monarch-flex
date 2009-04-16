<?php
require_once("./xmlWriter.php");
require_once('../database/Database.php');

$fullName = $_GET['fullName'];
$email =  $_GET['email'];
$password =  $_GET['password'];

$database = new Database('master');

$q = 'SELECT id FROM users WHERE username = "' . $email . '"';

if(mysql_num_rows($database->query($q)) > 0)
  {
    $xml = new cXmlWriter();
    $xml->push("LoggedInAccountInformation");
    $xml->push("UserAccountInformation");
    $xml->pop();
    $xml->pop();

    echo $xml->getXML();
  }
 else
   {
     $q = 'INSERT INTO users (username, realName, created, password) VALUES( "' . $email . '", 
           "' . $fullName . '", 
           "' . time() . '", 
           password("' . $password . '"))';
     $database->query($q);
     $_SESSION['userId'] = mysql_insert_id();

     $xml = new cXmlWriter();
     $xml->push("LoggedInAccountInformation");
     // store the user account information                                                                     
     $xml->push("UserAccountInformation");
     $xml->element("fullName", $fullName);
     $xml->element("email", $email);
     $xml->element("password", $password);
     $xml->element("userId", mysql_insert_id());
     $xml->pop();
     $xml->pop();
     
     echo $xml->getXML();
   }

?>
