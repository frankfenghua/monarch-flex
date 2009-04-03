<?php

	include "./xmlWriter.php";

	// connect to the mySQL server and select the database
	$host = 'csil-srprj-2.cs.uiuc.edu';  
	$user = 'ryan'; 
	$password = 'adobe';  
	$link = mysql_connect($host, $user, $password) or die ('Could not connect: ' . mysql_error()); 
	mysql_select_db('communityanalysis') or die ('Could not select database<br/>');

	$query = "SELECT * FROM websites";
	$result = mysql_query($query);

	$xml = new cXmlWriter();
	$xml->push("head");
	$xml->push("AllWebsites");
	$xml->element("numberOfWebsites", mysql_num_rows($result));
	
	for($i = 0; $i < mysql_num_rows($result); $i++)
	{
		$xml->push("Website".$i);
		
		$id = mysql_result($result, $i, "id");
		$name = mysql_result($result, $i, "name");
		$type = mysql_result($result, $i, "type");

		$xml->element("id", $id);
		$xml->element("name", $name);
		$xml->element("type", $type);

		$xml->pop();
	}
	$xml->pop();
	$xml->pop();
	echo $xml->getXML();
	
?>