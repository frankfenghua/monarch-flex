<?php

	$community = Array($_GET["mArgsArray"]);
	$userId = $community[0];
	$communityName = $community[1];
	$numWebsites = $community[2];
	
	for($i = 3; $i < sizeof($community); $i++)
		$websites[] = $community[$i];

	require_once('../database/Database.php');

	$database = new Database('master');

	$q = 'INSERT INTO communities (name, user)
	      VALUES("' . $communityName . '",
	             "' . $userId        . '")';
	
	$database->query($q);

	$communityId = mysql_insert_id();

	foreach($websites as $website)
	{
		$q = 'INSERT INTO websites (name, user, community)
		      VALUES("' . $website     . '", 
		             "' . $userId      . '", 
		             "' . $communityId . '")';

		$database->query($q);

		// FIX: create database for each website
	}

	echo '0';

?>