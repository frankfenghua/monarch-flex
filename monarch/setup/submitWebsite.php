<?php

// title:  submitWebsite.php
// author: Ryan Lin
// date:   Nov 6, 2008
// about:  Allows a user submit a website under some community
// ============================================================================

	require_once('../database/Database.php');
	
	$database = new Database('master');

	$q = 'SELECT id, name
	      FROM communities';
		  
	$q = $database->query($q);
	
	// no communities found
	if(mysql_num_rows($q) == 0)
	{
?>
		<h1>error: no communities were found</h1>
		You should <a href="?section=submitCommunity.php">create a community</a> so that you can insert some website under this community.
<?php
	}
	else
	{
?>
		<h1>submit a new website</h1>
		<div class="half">
			<div class="in">
				<form method="post" action="?section=insertWebsite">
					<b>community:</b>
					<select name="community">
<?php
		// list out all the communities
		while($row = mysql_fetch_array($q))
			printf('<option value="%d">%s</option>', $row['id'], $row['name']);
?>
					</select>
					<b>type:</b>
					<select name="type">
						<option>forum</option>
						<option>news</option>
						<option>blog</option>
					</select>			
					<b>website name:</b>
					<input type="text" name="websiteName" />
					<br />
					<div class="formButton">
						<input type="submit" value="submit" />
					</div>
				</form>
			</div>
		</div>
<?php
	}
?>
