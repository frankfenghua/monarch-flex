<?php

	// setWebsiteSettings.php
	//  -Script which accepts settings for the website passed in as
	//   'websiteName' and updates the database or creates new 
	//   database and initializes it.

	error_reporting(E_ALL);
	ini_set('display_errors','1');

	require_once('../database/Database.php');
	require_once('../Utilities.php');

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
	// =======================
	// ==Scraping Parameters==
	// =======================
        //    scrapeInterval
    	//    scrapeNumTopLevel

	// Using these values, it either initializes the new website
	// or updates information for an existing website


	print_r($_GET);

?>
