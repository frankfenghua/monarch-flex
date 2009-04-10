<?php

// title:  myAccount.php
// author: Ryan Lin
// date:   11/06/08
// about:  Let's user view the communities he's created and edit the regexs of
//         the websites that he's created.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	require_once('../database/Database.php');
	
	$database = new Database('master');
?>

<h1>Welcome</h1>

<div class="half">
	<div class="in">
		<h3>communities i've created</h3>
			<ul>
<?php
		$q = 'SELECT name 
		      FROM communities 
		      WHERE user = ' . $_SESSION['userId'];
		
		$q = $database->query($q);
		
		while($row = mysql_fetch_array($q))
			echo '<li>' . $row['name'] . '</li>';
?>
		</ul>
	</div>
</div>
<div class="half">
	<div class="in">
		<h3>edit regexes of my websites</h3>
			<ul>
<?php
		$q = 'SELECT id, name
		      FROM websites
		      WHERE user = ' . $_SESSION['userId'];
		
		$q = $database->query($q);
		
		while($row = mysql_fetch_array($q))
			printf('<li><a href="?section=regexEditor&websiteId=%d">%s</a></li>', $row['id'], $row['name']);
?>
		</ul>
	</div>
</div>
<br class="clear" />
<div class="half">
	<div class="in">
		<h3>things you can do</h3>
		<ul>
			<li><a href="?section=submitCommunity">Create a community</a>.</li>
			<li><a href="?section=submitWebsite">Create a website</a> under an existing community.</li>
		</ul>
	</div>
</div>
