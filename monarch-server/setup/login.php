<?php

// title:  login.php
// author: Ryan Lin
// date:   11/06/08
// about:  If the user entered in the correct credentials, sets a session 
//         variable to sign him in.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	require_once('../database/Database.php');

	/* FIX: Maurice, uncomment the below if you want to do this the Flex way. 
	        Then comment out the next block of code
	 
	$array = Array($_GET["mArgsArray"]);
	$username = $array[0];
	$password = $array[1];
	*/
	
	$username = mysql_real_escape_string($_POST['loginUsername']);
	$password = mysql_real_escape_string($_POST['loginPassword']);

	$database = new Database('master');
	
	$q = 'SELECT id 
	      FROM users 
	      WHERE username = "' . $username . '"
	      AND password = password("' . $password . '")';
	
	$q = $database->query($q);
	
	// incomplete form
	if(!$username || !$password)
	{
?>
		<h1>error logging in</h1>
		Please complete the entire login form. <a href="?section=home">Try Again</a>.
<?php	
	}
	// account not found
	else if(mysql_num_rows($q) == 0)
	{
?>
		<h1>error logging in</h1>
		Your password or username was incorrect. <a href="?section=home">Try Again</a>.
<?php
	}
	// success
	else
	{
		$q = mysql_fetch_array($q);
		
		$_SESSION['userId'] = $q['id'];
?>
		<h1>successfully logged in</h1>
		<ul>
			<li><a href="?section=submitCommunity">create communities</a>.</li>
			<li><a href="?section=submitWebsite">create websites</a> under existing communities.</li>
		</ul>
<?php
	}

?>