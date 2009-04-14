<?php

// detailStats.php
// Accepts parameter 'websiteID' and returns an XML document containing the 
// detailed statistics for the site.

header('Content-Type: text/xml');
?>

<root>
<general>
<?php
	// TODO: Query database mostActivePoster
	require_once('./database/Database.php');
	$database = new Database($_GET['websiteName']);
	$arr = ($database->fetch('SELECT COUNT(*) FROM posts'));
	$totalPosts = $arr[0];
	$arr = ($database->fetch('SELECT ((MAX(time) - MIN(time)) / 86400) FROM posts'));
	$totalDays = $arr[0];

	$mostActivePoster = '';
	echo '<postsPerDay>'.$totalPosts / $totalDays.'</postsPerDay>';
	echo '<mostActivePoster>'.$mostActivePoster.'</mostActivePoster>';
?>
</general>
<links>
<?php
// For each link
// <link>
// <mostMentioned> </mostMentioned>
// <leastMentioned> </leastMentioned>
// <mostFavorable> </mostFavorable>
// <leastFaborable> </leastFaborable>
// <worstSpoken> </worstSpoken>
// <bestSpoken> </bestSpoken>
// </link>
?>
</links>
<keywords>
<?php
// For each keyword
// <keyword>
// <favorableThreads>... </favorableThreads>
// <hateThreads>... </hateThreads>
// <mentionThreads>... </mentionThreads>
// <biggestLovers>... </biggestLovers>
// <sayer>... </sayer>
// </keyword>
?>
</keywords>
</root>
