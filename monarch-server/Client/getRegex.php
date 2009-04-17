<?php

	// getRegex.php
	//  -Simple script which takes a parameter websiteName or ID and returns
	//   the regular expressions for that website in XML

	error_reporting(E_ALL);
	ini_set('display_errors','1');
	header('content-type: text/xml');

	require_once('../database/Database.php');

	$database = new Database($_GET['websiteName']);

	$result = $database->query('SHOW COLUMNS FROM regexes');

	$cols = array();
	while ($row=mysql_fetch_row($result)){
		array_push($cols,$row[0]);
	}

	// $cols contains the names of the regexes
	
	$result = $database->fetch('SELECT * FROM regexes');

	echo '<regexes>';
	foreach($cols as $regexName) {
		echo '<'.$regexName.'>'.$result[$regexName].'</'.$regexName.'>';	
	}
	echo '</regexes>';
?>
