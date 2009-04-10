<?php

// title:  insertRegexes.php
// author: Ryan Lin
// date:   10/19/08
// about:  Inserts or updates a community's regexes
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	require_once('../database/Database.php');


	switch($_POST['linkStructure'])
	{
		case 'absolute':
			$linkStructure = 'absolute';
			break;
		
		case 'relative':
			$linkStructure = 'relative';
			break;
		
		case 'weirdRelative':
			$linkStructure = $_POST['weirdRelativeText'];
			break;
		
		default:
			die('ERROR: insertRegexes.php - unrecognized linkStructure.');
	}

	$database = new database($_POST['websiteId']);
	
	$q = 'SELECT id 
	      FROM regexes';
	
	$q = $database->query($q);
	
	// this is the first time user is writing regexes for this community
	if(mysql_num_rows($q) == 0)
	{
		$q = 'INSERT INTO regexes 
		      (startPage, linkStructure, nextPageOfThreads, nextPageOfPosts, threadUrl, 
		      threadNumPosts, threadNumViews, threadTitle, firstPostAuthor, firstPostTime,
		      firstPostMessage, replyAuthor, replyTime, replyMessage, id)
			  VALUES("' . mysql_real_escape_string($_POST['startPage'])         . '",
		             "' . mysql_real_escape_string($linkStructure)              . '", 
		             "' . mysql_real_escape_string($_POST['nextPageOfThreads']) . '",
		             "' . mysql_real_escape_string($_POST['nextPageOfPosts'])   . '",
		             "' . mysql_real_escape_string($_POST['threadUrl'])         . '",
		             "' . mysql_real_escape_string($_POST['threadNumPosts'])    . '",
		             "' . mysql_real_escape_string($_POST['threadNumViews'])    . '",
		             "' . mysql_real_escape_string($_POST['threadTitle'])       . '",
		             "' . mysql_real_escape_string($_POST['firstPostAuthor'])   . '", 
		             "' . mysql_real_escape_string($_POST['firstPostTime'])     . '", 
		             "' . mysql_real_escape_string($_POST['firstPostMessage'])  . '",
		             "' . mysql_real_escape_string($_POST['replyAuthor'])       . '", 
		             "' . mysql_real_escape_string($_POST['replyTime'])         . '", 
		             "' . mysql_real_escape_string($_POST['replyMessage'])      . '",
		             "0")';
	}
	// user is updating existing regexes
	else
	{
		$q = 'UPDATE regexes 
		      SET 
		      startPage         = "' . mysql_real_escape_string($_POST['startPage'])         . '",
		      linkStructure     = "' . mysql_real_escape_string($linkStructure)              . '",
		      nextPageOfThreads = "' . mysql_real_escape_string($_POST['nextPageOfThreads']) . '",
		      nextPageOfPosts   = "' . mysql_real_escape_string($_POST['nextPageOfPosts'])   . '",
		      threadUrl         = "' . mysql_real_escape_string($_POST['threadUrl'])         . '",
		      threadNumPosts    = "' . mysql_real_escape_string($_POST['threadNumPosts'])    . '", 
		      threadNumViews    = "' . mysql_real_escape_string($_POST['threadNumViews'])    . '", 
		      threadTitle       = "' . mysql_real_escape_string($_POST['threadTitle'])       . '",
		      firstPostAuthor   = "' . mysql_real_escape_string($_POST['firstPostAuthor'])   . '", 
		      firstPostTime     = "' . mysql_real_escape_string($_POST['firstPostTime'])     . '",
		      firstPostMessage  = "' . mysql_real_escape_string($_POST['firstPostMessage'])  . '",
		      replyAuthor       = "' . mysql_real_escape_string($_POST['replyAuthor'])       . '", 
		      replyTime         = "' . mysql_real_escape_string($_POST['replyTime'])         . '", 
		      replyMessage      = "' . mysql_real_escape_string($_POST['replyMessage'])      . '"
		      WHERE id = 0';
	}
	
	$database->query($q);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<title>Plugin Editor</title>
	
	<link href="reset.css" rel="stylesheet" type="text/css" />
	<link href="base.css" rel="stylesheet" type="text/css" />
	<link href="master.css" rel="stylesheet" type="text/css" />
	
	<script src="ajax.js" type="text/javascript"></script>
	<script src="scriptaculous/lib/prototype.js" type="text/javascript"></script>
	<script src="scriptaculous/src/scriptaculous.js?load=effects" type="text/javascript"></script>
</head>
<body>

<div id="contain">
	<h1>plugin editor</h1>
	
	<div id="instructions">
		<div>
			<h3>Success!</h3>
			Your regexes have been updated in the community's database.
		</div>
	</div>
	
</div>

</body>
</html>
