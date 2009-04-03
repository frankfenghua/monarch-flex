<?php
  
   require_once('./database/Database.php');
   require_once('./xmlWriter.php');
   $database = new Database('master');

   $communityId = $_GET['communityId'];
   $websiteIds = $_GET['websiteIds'];

   // extract the all of the website ids from the string
   $ids = Array();
   $array[0] = strtok($websiteIds, ',');

   $counter = 1;
   while(FALSE !== ($token = strtok(',')))
   {
     $array[$counter] = $token;
     $counter++;
   }

   $xml = new cXMLWriter();
   $xml->push("ids");
   
   for($i = 0; $i < sizeof($array); $i++)
   {
     $id = $array[$i];
     $xml->element("id", $id);
   }

   $xml->pop();
   echo $xml->getXML();

?>