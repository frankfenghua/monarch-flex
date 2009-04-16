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
}
// add keyword to community
else if($type === "ak") {
	$query = "INSERT INTO allowedkeywords (community, word) 
			 VALUES ('" . $communityId . "','" . $name . "')";
	$database->query($query);
}
// remove website from community
else if($type === "rw") {
	
}
else {
	echo "-1";
}

?>