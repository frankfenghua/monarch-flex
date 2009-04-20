<?php

require_once("./xmlWriter.php");
require_once('./../database/Database.php');

$database = new Database('master');

$userId = $_GET['userId'];
$community = $_GET['community'];
$websiteName = $_GET['websiteName'];
$websiteURL = $_GET['websiteURL'];
$type = $_GET['type'];

//echo $userId . " " . $community . " " . $websiteName . " " . $websiteURL . " " . $type;

$websiteName = strtolower($websiteName);
$websiteName = str_replace(' ', '', $websiteName);

$query = 'SELECT id FROM websites WHERE name = "' . $websiteName . '"';

$query = $database->query($query);

$num_rows = mysql_num_rows($query);

if($num_rows > 0)
  {
    echo "-1";
  }
 else
   {
     $time = time();
     $query = 'INSERT INTO websites (user, community, name, created, type)
              VALUES("' . $userId . '",
              "' . $community . '",
              "' . $websiteName . '",
              "' . $time . '",
              "' . $type . '")';
     
     $database->query($query);
     
     $websiteId = mysql_insert_id();
    
     $database = new Database('root');
     
     $database->query('CREATE DATABASE ' . $websiteName);
     
     $database = new Database(strtolower($websiteName));

     // causes problem
     $database->query(file_get_contents('./database/website_temp.txt'));

     $xml = new cXmlWriter();
     $xml->push("NewlyInsertedWebsite");
     $xml->push("WebsiteInformation");
     
     // store the website id, name, and URL
     $xml->element("websiteId", $websiteId);
     $xml->element("websiteName", $websiteName);
     $xml->element("websiteURL", $websiteURL);
     $xml->element("websiteType", $type);
     $xml->element("websiteCreatedTime", $time);
     $xml->pop();

     $xml->pop();

     echo $xml->getXML();
   }

?>
