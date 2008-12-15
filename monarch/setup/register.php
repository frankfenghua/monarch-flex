<?php 

// title:  register.php
// author: Ryan Lin
// date:   11/06/08
// about:  Attempts to register a user. Will give error messages if the 
//         registration form was no filled out correctly or the username 
//         already exists. Automatically logs the user in if he successfully 
//         gets registered.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// FIX: Maurice, uncomment this section and comment out the next section
	/*
	$accountData = Array($_GET["mArgsArray"]);

	$username = $accountData[0];
	$password = $accountData[1];
	$fullName = $accountData[2];
	*/
	
	require_once('../database/Database.php');
	
	// remove SQL injection attacks
	$username      = mysql_real_escape_string($_POST['username']);
	$realName      = mysql_real_escape_string($_POST['realName']);
	$password      = mysql_real_escape_string($_POST['password']);
	$passwordAgain = mysql_real_escape_string($_POST['passwordAgain']);

	$database = new Database('master');

	// check for duplicate username
	$q = 'SELECT id 
	      FROM users 
	      WHERE username = "' . $username . '"';

	// missing some field
	if(!$username || !$realName || !$password || !$passwordAgain)
	{
?>
		<h1>Error Registering</h1>
		You were missing a field. Please <a href="?section=home">try Again</a>.
<?php
	}
	// passwords don't match
	else if($password != $passwordAgain)
	{
?>
		<h1>Error Registering</h1>
		Please enter the two password fields identically. <a href="?section=home">Try Again</a>.
<?php
	}
	// account already exists
	else if(mysql_num_rows($database->query($q)) > 0)
	{
?>
		<h1>Error Registering</h1>
		The username already exists. Please <a href="?section=home">enter a different username</a>.
<?php
	}
	// account is unique - register the user
	else
	{
		$q = 'INSERT INTO users (username, realName, created, password)
		      VALUES(  "' . $username . '",
	    	           "' . $realName . '",
		               "' . time()    . '", 
	    	  password("' . $password . '"))';

		$database->query($q);
?>
		<h1>Successfully Registered</h1>
		You have been registered. You may now <a href="?section=submitCommunity">create communities</a> 
		or <a href="?section=submitWebsite">create websites</a> under existing communities.
<?php
		// log user in automatically
		$_SESSION['userId'] = mysql_insert_id();

		// FIX: Maurice, uncommenting this will print out the user ID that was just inserted
		// echo mysql_insert_id(); 
	}	

?>