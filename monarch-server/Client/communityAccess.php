<?php
	// communityAccess.php
	// Accepts parameter "communityID" and increments the accessCount value of that community
	// written by Andrew Spencer, 2009


	// Show errors
	error_reporting(E_ALL);
	ini_set('display_errors','1');

	require_once("../database/Database.php");

	// Connect to database
	$database = new Database('master');
	if($_GET["communityID"]) {
		$database->query('UPDATE communities
					SET accessCount = accessCount + 1
					WHERE id = '.$_GET["communityID"]);	
	}
?>
