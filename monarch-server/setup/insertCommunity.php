<?php

// title:  insertCommunity.php
// author: Ryan Lin
// date:   Nov 6, 2008
// about:  Inserts a community into the database if it is not a duplicate.
// ============================================================================

	require_once('../database/Database.php');
	
	$database = new Database('master');
	
	$comName = mysql_real_escape_string($_POST['name']);
	
	$q = 'SELECT id
	      FROM communities
	      WHERE name = "' . $comName . '"';
	
	$q = $database->query($q);
	
	// duplicate name
	if(mysql_num_rows($q) > 0)
	{
?>
		<h1>error creating community</h1>
		This community already exists. Please <a href="?section=submitCommunity">choose a different name</a>.
<?php
	}
	// success
	else
	{
		// insert community
		$q = 'INSERT INTO communities (user, name, created)
		      VALUES("' . $_SESSION['userId'] . '",
		             "' . $comName            . '",
		             "' . time()              . '")';
					 
		$database->query($q);
		
		$communityId = mysql_insert_id();
		
		// insert keywords this community scrapes for
		$i = 1;
		
		while($_POST['keyword' . $i])
		{
			$keyword = mysql_real_escape_string($_POST['keyword' . $i]);
			
			$q = 'INSERT INTO allowedKeywords (community, word)
			      VALUES("' . $communityId . '",
			             "' . $keyword     . '")';
			
			$database->query($q);
			
			$i++;
		}
		
?>
		<h1>successfully created community</h1>
		<ul>
			<li><a href="?section=submitCommunity">create another community</a></li>
			<li><a href="?section=submitWebsite">create a website</a> under existing communities.</li>
		</ul>
<?php
		
	}