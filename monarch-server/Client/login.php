<?php
require_once("./xmlWriter.php");
error_reporting(E_ALL);
ini_set('display_errors','1');

require_once('../database/Database.php');

$email = $_GET['email'];
$password = $_GET['password'];

$database = new Database('master');

$q = 'SELECT id 
      FROM users 
      WHERE username = "' . $email . '"
      AND password = password("' . $password . '")';

$q = $database->query($q);

if(mysql_num_rows($q) == 0)
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
     $q = mysql_fetch_array($q);
     $_SESSION['userId'] = $q['id'];

     $realName = 'SELECT realName 
                  FROM users 
                  WHERE username = "' . $email . '"
                  AND password = password("' . $password . '")';
     
     $realName = $database->query($realName);
     $realName = mysql_fetch_array($realName);

     $xml = new cXmlWriter();
     $xml->push("LoggedInAccountInformation");
     // store the user account information
     $xml->push("UserAccountInformation");
     $xml->element("fullName", $realName[0]);
     $xml->element("email", $email);
     $xml->element("password", $password);
     $xml->element("userId", $q[0]);
     $xml->pop();
     // store the user's community group information along with the websites
     // and keywords
     $xml->push("MyCommunityGroups");
         $query = 'SELECT * FROM communities WHERE user = "' . $q[0] . '"';
         $result = $database->query($query);
	 $num_rows = mysql_num_rows($result);
	 $xml->element("NumberOfCommunityGroups", $num_rows);
	 for($i = 0; $i < $num_rows; $i++)
	 {
       	$xml->push("CommunityGroup".$i);
		$commId = mysql_result($result, $i, "id"); 
        $commName = mysql_result($result, $i, "name");
		$time = mysql_result($result, $i, "created");
		$xml->element("communityGroupId", $commId);
		$xml->element("communityGroupName", $commName);
		$xml->element("communityGroupCreatedTime", $time);

		// get the creator of the community group
		$creatorQuery = "SELECT user FROM communities WHERE id = '" . $commId . "'";
		$creatorResult = mysql_query($creatorQuery);
		$creatorId = mysql_result($creatorResult, 0, "user");
		$creatorQuery = "SELECT username FROM users WHERE id = '" . $creatorId . "'";
		$creatorResult = mysql_query($creatorQuery);
		$creatorName = mysql_result($creatorResult, 0, "username");
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
	
	 // fetch community groups which don't belong to the user
	 $xml->push("OtherCommunityGroups");
         $query = 'SELECT * FROM communities WHERE user != "' . $q[0] . '"';
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

	     $xml->pop(); // from $xml->push("LoggedInAccountInformation");
		 // return xml data
     	echo $xml->getXML();
   	}
?>
