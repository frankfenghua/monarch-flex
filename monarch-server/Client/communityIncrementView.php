<?php

	require_once('../database/Database.php');
	
	$database = new Database('master');
	
	$q = 'UPDATE communities
		SET accessCount = accessCount + 1
		WHERE id = ' . $_GET['communityId'];
		
	$database->query($q);

?>