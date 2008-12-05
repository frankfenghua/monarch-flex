<?php
ini_set($display_errors, 1);
require_once('../database/Database.php');
$username = "Jerry";
$realName = "Jerry";
$password = "hellow";
$database = new Database('master');
		$q = 'INSERT INTO users (username, realName, created, password)
		      VALUES(  "' . $username . '",
	    	           "' . $realName . '",
		               "' . time()    . '", 
	    	  password("' . $password . '"))';

$database->query($q);
?>
