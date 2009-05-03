<?php
require_once("./xmlWriter.php");
require_once('../database/Database.php');

// type specifies which operation should be performed using
// name and communityId. 
// "rk" : remove keyword from community
// "ak" : add keyword to community
// "rw" : remove website from community
$type = $_GET['type'];
// string specific to the type
$name =  $_GET['name'];
// community id
$communityId =  $_GET['communityId'];

$database = new Database('master');
// remove a keyword from community
if($type === "rk") {
	$query = "DELETE FROM allowedkeywords WHERE community = '" .
			  $communityId . "' AND word = '" . $name . "'";
	$database->query($query);

	$xml = new cXmlWriter();
	$xml->push("RemovedKeyword");
	$xml->push("Information");
	
	$id = mysql_result($result, 0, "id");

	$xml->element("id", "-1");
	$xml->element("communityId", $communityId);
	$xml->element("name", $name);
	$xml->element("type", "rk");

	$xml->pop();
	$xml->pop();
	echo $xml->getXML();
}
// add keyword to community
else if($type === "ak") {
	$query = "INSERT INTO allowedkeywords (community, word) 
			 VALUES ('" . $communityId . "','" . $name . "')";
	$database->query($query);

	$query = "SELECT id FROM allowedkeywords WHERE community = '" .
	                 $communityId . "' AND word = '" . $name . "'";
	$result = $database->query($query);
	
	$xml = new cXmlWriter();
	$xml->push("NewKeyword");
	$xml->push("Information");
	
	$id = mysql_result($result, 0, "id");

	$xml->element("id", $id);
	$xml->element("communityId", $communityId);
	$xml->element("name", $name);
	$xml->element("type", "ak");

	$xml->pop();
	$xml->pop();
	echo $xml->getXML();
}
// remove website from community
else if($type === "rw") {
	$database->query("DELETE * FROM websites WHERE community = " . $communityId . " AND name = " . $name);
}
else {
	echo "-1";
}

?>