<?php
  
	require_once('./../database/Database.php');
	require_once('./xmlWriter.php');
	$database = new Database('master');
	
	// sanity checks
	if(!isset($_GET['userId']) || !isset($_GET['communityId']) || !isset($_GET['websiteIds']))
		die('USAGE: insertExistingWebsites.php?userId=[#]&communityId=[#]&websiteIds=[#],[#],...');
	
	if(!is_numeric($_GET['userId']) || !is_numeric($_GET['communityId']))
		die('ERROR: userId and communityId must be numeric');
	
	// remove hack attacks
	$userId      = mysql_real_escape_string($_GET['userId']);
	$communityId = mysql_real_escape_string($_GET['communityId']);
	$websiteIds  = explode(',', mysql_real_escape_string($_GET['websiteIds']));
	
	foreach($websiteIds as $websiteId)
	{
		if(!is_numeric($websiteId))
			die('ERROR: some website ID was not numeric');
	
		// find old website info that will remain the same
		$q = 'SELECT name, type, scrapeNumTopLevel, scrapeInterval
			FROM websites
			WHERE id = ' . $websiteId;
		
		$q = $database->fetch($q);
		
		// insert new website info with new owner, time, and community
		$z = 'INSERT INTO websites(user, community, name, created, type, scrapeNumTopLevel, scrapeInterval)
			VALUES("' . $userId              . '",
				"' . $communityId            . '",
				"' . $q['name']              . '",
				"' . time()                  . '",
				"' . $q['type']              . '",
				"' . $q['scrapeNumTopLevel'] . '",
				"' . $q['scrapeInterval']    . '")';
		
		$database->query($z);
	}

	/*
   // extract the all of the website ids from the string
   $ids = Array();
   $array[0] = strtok($websiteIds, ',');

   $counter = 1;
   while(FALSE !== ($token = strtok(',')))
   {
     $array[$counter] = $token;
     $counter++;
   }

   $xml = new cXMLWriter();
   $xml->push("ids");
   
   for($i = 0; $i < sizeof($array); $i++)
   {
     $id = $array[$i];
     $xml->element("id", $id);
   }

   $xml->pop();
   echo $xml->getXML();
   */

?>