<?php
require_once("./xmlWriter.php");
require_once('../database/Database.php');

$database = new Database('master');

$comName = $_GET['communityName'];
$userId = $_GET['userId'];

$query = 'SELECT id FROM communities WHERE name = "' . $comName . '"';

$query = $database->query($query);

if(mysql_num_rows($query) > 0)
  {
    echo "-1";
  }
 else
   {
     $xml = new cXmlWriter();
     $xml->push("NewlyInsertedCommunity");
     $xml->push("CommunityInformation");

     $time = time();
     $query = 'INSERT INTO communities (user, name, created)
              VALUES("' . $userId . '",
              "' . $comName . '",
              "' . $time . '")';
     $database->query($query);
     $communityId = mysql_insert_id();

     // store the community id and name
	 // get the creator of the community group
	 $creatorQuery = "SELECT user FROM communities WHERE id = '" . $communityId . "'";
	 $creatorResult = mysql_query($creatorQuery);
	 $creatorId = mysql_result($creatorResult, 0, "user");
	 $creatorQuery = "SELECT username FROM users WHERE id = '" . $creatorId . "'";
	 $creatorResult = mysql_query($creatorQuery);
	 $creatorName = mysql_result($creatorResult, 0, "username");

     $xml->element("communityGroupId", $communityId);
     $xml->element("communityGroupName", $comName);
     $xml->element("communityGroupCreatedTime", $time);
	 $xml->element("communityGroupCreator", $creatorName);	
     $xml->pop();

     // store the community keywords
     $xml->push("CommunityGroupKeywords");

     $i = 1;
     while($_GET['keyWord' . $i])
       {
	 $xml->push("Keyword".($i - 1));

	 $keyword = $_GET['keyWord' . $i];
	 $query = 'INSERT INTO allowedkeywords (community, word)
                   VALUES("' . $communityId . '",
                   "' . $keyword . '")';

	 $database->query($query);
	 $i++;

	 $xml->element("keywordId", mysql_insert_id());
	 $xml->element("keywordName", $keyword);

	 $xml->pop();
       }

     $xml->pop();
     $xml->pop();

     echo $xml->getXML();
   }
?>