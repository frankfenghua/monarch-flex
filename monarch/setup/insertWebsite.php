<?php

// title:  insertWebsite.php
// author: Ryan Lin
// date:   Nov 6, 2008
// about:  Inserts a website under a community if it's not a duplicate.
// ============================================================================

	require_once('../database/Database.php');
	
	$database = new Database('master');
	
	$community   = mysql_real_escape_string($_POST['community']);
	$websiteName = mysql_real_escape_string($_POST['websiteName']);
	$type        = mysql_real_escape_string($_POST['type']);
	
	$q = 'SELECT id
	      FROM websites
	      WHERE name = "' . $websiteName . '"';
	
	$q = $database->query($q);
	
	// duplicate website
	if(mysql_num_rows($q) > 0)
	{
?>
		<h1>error creating website</h1>
		This website already exists. Please <a href="?section=submitWebsite">choose a different name</a>.
<?php
	}
	// success
	else
	{
		$q = 'INSERT INTO websites (user, community, name, created, type)
		      VALUES("' . $_SESSION['userId'] . '",
		             "' . $community          . '",
		             "' . $websiteName        . '",
		             "' . time()              . '",
		             "' . $type               . '")';
					 
		$database->query($q);
		
		$websiteId = mysql_insert_id();
		
		// create a new database for this website
		$database = new Database('root');
		$database->query('CREATE DATABASE ' . $websiteName);
		
		// FIX: figure out why database names automatically get lowercased when created
		$database = new Database(strtolower($websiteName));
		
		// FIX: query gives error even though when i copy and paste it into phpMyAdmin, it works
		$database->query(file_get_contents('../database/website.txt'));
?>
		<h1>successfully created website</h1>
		<ul>
			<li><a href="?section=regexEditor&websiteId=<?php echo $websiteId; ?>">Setup regular expressions for your new site</a>.</li>
			<li><a href="?section=submitCommunity">Create a community</a>.</li>
			<li><a href="?section=submitWebsite">Create another website</a> under an existing community.</li>
		</ul>
<?php	
	}
?>