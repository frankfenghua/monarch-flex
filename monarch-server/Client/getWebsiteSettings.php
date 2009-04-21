<?php

	// getWebsiteSettings.php
	//  -Simple script which takes a parameter 'websiteName' (or ID) and returns
	//   the regular expressions and scraping parameters for that website in XML

	error_reporting(E_ALL);
	ini_set('display_errors','1');

	require_once('../database/Database.php');

	// ======================================================
	// Function definitions
	// ======================================================

	// ======================================================
	// removePregSyntax
	//   args:  string - a string representing a Perl-style regular 
	//            expression (i.e. '/[a-z]+/'
	//   ret:   string which does not have Perl-style specific features
	//   about: Converts the parameter to a not exclusively Perl-style
	//            regular expression. Ex. '/[a-z]+/' => '[a-z]+'
	function removePregSyntax($str) {
		if($str[0] == '/') {
			return substr($str, 1, -1);
		}
		else {
			return $str;
		}
	}
	


	// ======================================================
	// Main execution
	// ======================================================


	$database = new Database($_GET['websiteName']);

	$result = $database->query('SHOW COLUMNS FROM regexes');

	// Store column names in $cols
	$cols = array();
	while ($row=mysql_fetch_row($result)){
		array_push($cols,$row[0]);
	}

	$result = $database->fetch('SELECT * FROM regexes');

	// Output in flashvars format
	foreach($cols as $regexName) {
		$regexValue = removePregSyntax($result[$regexName]);
		echo $regexName.'='.$regexValue.';';	
	}

	// Retrieve scraping parameters
	$database = new Database('master');

	$result = $database->fetch('SELECT scrapeNumTopLevel, scrapeInterval FROM websites
					WHERE name = "' . $_GET['websiteName'] . '"');

	echo 'scrapeNumTopLevel' . '=' . $result['scrapeNumTopLevel'] . ';';
	echo 'scrapeInterval' . '=' . $result['scrapeInterval'] . ';';
?>
