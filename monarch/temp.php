<?php
	require_once('database/Database.php');
	
	$db = new Database('threadless');
	
	$q = 'INSERT INTO threads (title) VALUES("and&and")';
	
	$db->query($q);
?>

