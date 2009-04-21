<?php

	// setWebsiteSettings.php
	//  -Script which accepts settings for the website passed in as
	//   'websiteName' and updates the database or creates new 
	//   database and initializes it.

	error_reporting(E_ALL);
	ini_set('display_errors','1');

	require_once('../database/Database.php');
	//require_once('../Utilities.php'); // TODO: what is this file?

	// ======================================================
	// Function definitions
	// ======================================================

	// ======================================================
	// Main execution
	// ======================================================


	// What this needs to do:
	//  The GUI sends the following key-value pairs via GET:
	// =======================
	// ==Website Information==
	// =======================
        //    websiteName
        //    communityId
		//    userId TODO: ############################## you didn't have this ###############################
		//    type   TODO: ############################## you didn't have this ###############################
	// =======================
	// ==Regular Expressions==
	// =======================
    	//    replyAuthor
	//    firstPostMessage
        //    nextPageOfThreads
        //    replyMessage
        //    threadNumViews
        //    firstPostTime
        //    threadTitle
        //    firstPostAuthor
        //    nextPageOfPosts
        //    firstPostAuthorUrl
        //    replyAuthorUrl
        //    replyTime
        //    threadUrl
		//    startPage   TODO: ############################## you didn't have this ###############################
	// =======================
	// ==Scraping Parameters==
	// =======================
        //    scrapeInterval
    	//    scrapeNumTopLevel

	// Using these values, it either initializes the new website
	// or updates information for an existing website
	
	$database = new Database('master');
	
	// Remove all hacks and decode anything that could have had the entities for anything 
	// that could screw up the argument string.
	// Note: some MySQL configurations cannot handle database names with caps.
	$websiteName        = mysql_real_escape_string(strtolower($_GET['websiteName']));
	$communityId        = mysql_real_escape_string($_GET['communityId']);
	$userId             = mysql_real_escape_string($_GET['userId']);
	$type               = mysql_real_escape_string($_GET['type']);
	$replyAuthor        = mysql_real_escape_string(html_entity_decode($_GET['replyAuthor']));
	$firstPostMessage   = mysql_real_escape_string(html_entity_decode($_GET['firstPostMessage']));
	$nextPageOfThreads  = mysql_real_escape_string(html_entity_decode($_GET['nextPageOfThreads']));
	$replyMessage       = mysql_real_escape_string(html_entity_decode($_GET['replyMessage']));
	$threadNumViews     = mysql_real_escape_string(html_entity_decode($_GET['threadNmViews']));
	$firstPostTime      = mysql_real_escape_string(html_entity_decode($_GET['firstPostTime']));
	$nextPageOfPosts    = mysql_real_escape_string(html_entity_decode($_GET['nextPageOfPosts']));
	$firstPostAuthorUrl = mysql_real_escape_string(html_entity_decode($_GET['firstPostAuthorUrl']));
	$replyAuthorUrl     = mysql_real_escape_string(html_entity_decode($_GET['replyAuthorUrl']));
	$replyTime          = mysql_real_escape_string(html_entity_decode($_GET['replyTime']));
	$threadUrl          = mysql_real_escape_string(html_entity_decode($_GET['threadUrl']));
	$startPage          = mysql_real_escape_string(html_entity_decode($_GET['startPage']));
	$scrapeInterval     = mysql_real_escape_string($_GET['scrapeInterval']);
	$scrapeNumTopLevel  = mysql_real_escape_string($_GET['scrapeNumTopLevel']);
	
	// type checks
	if(!is_numeric($communityId) ||
		!is_numeric($userId) ||
		!is_numeric($scrapeInterval) ||
		!is_numeric($scrapeNumTopLevel))
		die('Error: check that all numeric types only contain numbers');
	
	// weird characters are not allowed to be database names
	if(!ctype_alnum($websiteName))
		die('Error: website name was not alphanumeric');
	
	// attempt to find if the website exists
	$q = 'SELECT id 
		FROM websites 
		WHERE name = "' . $websiteName . '"';
		
	$q = $database->query($q);
	
	// you are creating the website from scratch
	if(mysql_num_rows($q) == 0)
	{
		$q = 'INSERT INTO websites (user, community, name, created, type, scrapeNumTopLevel, scrapeInterval) 
			VALUES (
			"' . $userId            . '", 
			"' . $communityId       . '", 
			"' . $websiteName       . '", 
			"' . time()             . '", 
			"' . $type              . '", 
			"' . $scrapeNumTopLevel . '", 
			"' . $scrapeInterval    . '")';
			
		$database->query($q);
		
		$websiteId = mysql_insert_id();
		
		// create the database for this new website
		$database = new Database('root');
		$database->query('CREATE DATABASE IF NOT EXISTS ' . $websiteName);
		$database = new Database($websiteName);
		$database->query(file_get_contents('../database/website.txt'), true);	
		
		// insert regexes
		$q = 'INSERT INTO regexes
			VALUES(
			"' . $startPage         . '",
			"' . $nextPageOfThreads . '",
			"' . $nextPageOfPosts   . '", 
			"' . $threadUrl         . '", 
			"' . $threadNumViews    . '",
			"' . $threadTitle       . '",
			"' . $firstPostAuthor   . '",
			"' . $firstPostTime     . '",
			"' . $firstPostMessage  . '",
			"' . $replyAuthor       . '",
			"' . $replyAuthorUrl    . '",
			"' . $replyTime         . '",
			"' . $replyMessage      . '")';
			
		$database->query($q);			
	}
	// you are editing an existing website
	else
	{	
		$q = $database->fetch($q);
		
		$websiteId = $q['id'];
		
		$q = 'UPDATE websites
			SET
			scrapeNumTopLevel = "' . $scrapeNumTopLevel . '",
			scrapeInterval    = "' . $scrapeInterval    . '"
			WHERE id = ' . $websiteId;
			
		$database->query($q);
		
		// TODO: you can't change the website name, because starting with MySQL 5, 
		// there is some security lock on changing database names
		// So I will only be updating the regexes and scrape timing:
		
		$database = new Database($websiteId);
		
		$q = 'UPDATE regexes 
			SET
			startPage         = "' . $startPage         . '",
			nextPageOfThreads = "' . $nextPageOfThreads . '",
			nextPageOfPosts   = "' . $nextPageOfPosts   . '", 
			threadUrl         = "' . $threadUrl         . '", 
			threadNumViews    = "' . $threadNumViews    . '",
			threadTitle       = "' . $threadTitle       . '",
			firstPostAuthor   = "' . $firstPostAuthor   . '",
			firstPostTime     = "' . $firstPostTime     . '",
			firstPostMessage  = "' . $firstPostMessage  . '",
			replyAuthor       = "' . $replyAuthor       . '",
			replyAuthorUrl    = "' . $replyAuthorUrl    . '",
			replyTime         = "' . $replyTime         . '",
			replyMessage      = "' . $replyMessage      . '")';
			
		$database->query($q);
	}
	
	echo 'website info succesfully saved';

	print_r($_GET);

?>
