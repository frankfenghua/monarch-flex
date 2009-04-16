
<?php
	
require_once("./xmlWriter.php");

// hostname or ip of server (for local testing, localhost should work)
$dbServer='localhost';

// username and password to log onto db server
$dbUser='ryan';
$dbPass='adobe';

// name of database
$dbName='communityanalysis';

    $link = mysql_connect("$dbServer", "$dbUser", "$dbPass") or die("Could not connect");
    //print "Connected successfully<br>";
    mysql_select_db("$dbName") or die("Could not select database");
    //print "Database selected successfully<br>";
	
	$query = 'SELECT * FROM websites';
	$result = mysql_query($query, $link);
	// get the number of rows for the table
	$num_rows = mysql_num_rows($result);
	
	$xml = new cXmlWriter();
	
	$xml->push("MasterRecordsDatabase");
	$xml->push("Communities");
	
	for($i = 0; $i < $num_rows; $i++)
	{
		$xml->push("Community".$i);
		$xml->element("id", mysql_result($result, $i, "id"));
		$xml->element("domain", mysql_result($result, $i, "domain"));
		$xml->element("type", mysql_result($result, $i, "type"));
		$xml->pop();
	}
	
	$xml->pop();
	$xml->pop();
	
	echo $xml->getXML();
	
	/*
	if($_GET['getData1'] == "1")
	{
		$xml = new cXmlWriter();
		
		$xml->push("Person");
		$xml->push("Male");
		
		$xml->element("Name", "Mariusz");
		$xml->element("Age", "24");
		
		$xml->pop();
		$xml->pop();
		
		echo $xml->getXML();
	}
	else
	{
		$xml = new cXmlWriter();
		
		$xml->push("Person");
		$xml->push("Male");
		
		$xml->element("Name", "Luke");
		$xml->element("Age", "20");
		
		$xml->pop();
		$xml->pop();
		
		echo $xml->getXML();
	}*/

	
// close connection
mysql_close($link);

?>
