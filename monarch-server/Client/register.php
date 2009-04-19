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
	
	 // fetch community groups which don't belong to the user
	 $xml->push("OtherCommunityGroups");
         $query = 'SELECT * FROM communities WHERE user != "' . mysql_insert_id() . '"';
         $result = $database->query($query);
	 $num_rows = mysql_num_rows($result);
	 $xml->element("NumberOfCommunityGroups", $num_rows);
	 for($i = 0; $i < $num_rows; $i++)
	 {
		$commId = mysql_result($result, $i, "id"); 

		// get the creator of the community group
		$creatorQuery = "SELECT user FROM communities WHERE id = '" . $commId . "'";
		$creatorResult = mysql_query($creatorQuery);
		$creatorId = mysql_result($creatorResult, 0, "user");
		$creatorQuery = "SELECT username FROM users WHERE id = '" . $creatorId . "'";
		$creatorResult = mysql_query($creatorQuery);
		$creatorName = mysql_result($creatorResult, 0, "username");

     	$xml->push("CommunityGroup".$i);
       	$commName = mysql_result($result, $i, "name");
		$time = mysql_result($result, $i, "created");
		$xml->element("communityGroupId", $commId);
		$xml->element("communityGroupName", $commName);
		$xml->element("communityGroupCreatedTime", $time);
		$xml->element("communityGroupCreator", $creatorName);		
		
		$xml->push("CommunityGroup".$i."Websites");
		$websiteQuery = 'SELECT * FROM websites WHERE community = "' . $commId . '"';
		$websiteResult = $database->query($websiteQuery);
		$websiteNum_rows = mysql_num_rows($websiteResult);
		$xml->element("NumberOfWebsites", $websiteNum_rows);
		for($j = 0; $j < $websiteNum_rows; $j++)
	  	{
		    $xml->push("Website".$j);
		    $websiteId = mysql_result($websiteResult, $j, "id");
		    $websiteName = mysql_result($websiteResult, $j, "name");
		    $websiteType = mysql_result($websiteResult, $j, "type");
		    $websiteCreatedTime = mysql_result($websiteResult, $j, "created");
		    $xml->element("websiteId", $websiteId);
		    $xml->element("websiteName", $websiteName);
		    $xml->element("websiteType", $websiteType);
		    $xml->element("websiteCreatedTime", $websiteCreatedTime);
		    $xml->pop();
		}
		$xml->pop();

		$xml->push("CommunityGroup".$i."Keywords");
		$keywordQuery = 'SELECT * FROM allowedkeywords WHERE community = "' . $commId . '"';
		$keywordResult = $database->query($keywordQuery);
		$keywordNum_rows = mysql_num_rows($keywordResult);
		$xml->element("NumberOfKeywords", $keywordNum_rows);
		for($j = 0; $j < $keywordNum_rows; $j++)
		{
		  	$xml->push("Keyword".$j);
		 	$keywordId = mysql_result($keywordResult, $j, "id");
			$keywordName = mysql_result($keywordResult, $j, "word");
			$xml->element("keywordId", $keywordId);
			$xml->element("keywordName", $keywordName);
		    $xml->pop();
		 }
		 $xml->pop();
	     $xml->pop();
	 }

     $xml->pop();
     $xml->pop();
     
     echo $xml->getXML();
   }

?>
