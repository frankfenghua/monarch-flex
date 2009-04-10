<?php

require_once('../database/Database.php');

$communityId = $_GET['community'];
$websiteName = $_GET['websiteName'];

$database = new Database('communityanalysis');

$q = 'SELECT * FROM allowedkeywords
      WHERE community = "' . $communityId . '"';

// grab all of the keywords which pertain to the community passed in
$q = $database->query($q);
$keywordArray = Array();
for($i = 0; $i < mysql_num_rows($q); $i++) {
    $keywordArray[$i] = mysql_result($q, $i, "word");
  }

// switch over to the database which represents our website
//$database = new Database($websiteName);
$database = new Database($websiteName);

// create an XML object
$xml = new cXmlWriter();
$xml->push("WebsiteAnalytics");
$xml->push("Information");
   $xml->element("communityId", $communityId);
   $xml->element("websiteName", $websiteName);
$xml->pop();
// Keyword Data Section starts here
$xml->push("Keywords");

// loop through database keywords and try to fetch each one
// from the website database keyword list
$counter = 0;
for($i = 0; $i < count($keywordArray); $i++)
  {
    $q = 'SELECT id FROM keywords WHERE word = "' . $keywordArray[$i] . '"';
    $q = $database->query($q);
    if(mysql_num_rows($q) > 0)
      {
	// store the keyword
	$xml->push("Keyword" . $counter);
	$xml->push("KeywordInformation");
	  $xml->element("keywordName", $keywordArray[$i]);
	$xml->pop();

	$counter++;
	
	$xml->push("Data");
	$keywordId = mysql_fetch_array($q);
	// once we have the keyword id we need to grab all of the stat table
	// id values which relate to the keyword id
	$q1 = 'SELECT * FROM keywordstats WHERE keyword = "' . $keywordId[0] . '"';
	$q1 = $database->query($q1);
	// now we need to iterate through the stats table for each stat id
	// and retrieve the stats for this keyword
	for($j = 0; $j < mysql_num_rows($q1); $j++)
	  {
	    $xml->push("Stat" . $j);
	    $statId = mysql_result($q1, $j, "stat");
	    // finally, we access the stats table and retrieve that stats with
	    // the statId
	    $q2 = 'SELECT * FROM stats WHERE id = "' . $statId . '"';
	    $q2 = $database->query($q2);
	    if(mysql_num_rows($q2) > 0)
	      {
		// store the analysis data
		$time = mysql_result($q2, 0, "time");
		$count = mysql_result($q2, 0, "count");
		$goodness = mysql_result($q2, 0, "goodness");
		$englishProficiency = mysql_result($q2, 0, "englishProficiency");
		$xml->element("time", $time);
		$xml->element("count", $count);
		$xml->element("goodness", $goodness);
		$xml->element("englishProficiency", $englishProficiency);
	      }
	    $xml->pop();
	  }
	$xml->pop();
	$xml->pop();
      }
  }
$xml->pop();

// Links Data Section starts here
$xml->push("Links");

$q1 = 'SELECT * FROM links';
$q1 = $database->query($q1);
//loop through all of the links in the links table
for($i = 0; $i < mysql_num_rows($q1); $i++)
  {
    $xml->push("Link" . $i);
    $xml->push("LinkInformation");
    $xml->element("linkName", mysql_result($q1, $i, "baseUrl"));
    $xml->pop();

    $xml->push("Data");
    $linkId = mysql_result($q1, $i, "id");		  
    $q2 = 'SELECT * FROM linkstats WHERE link = "' . $linkId . '"';
    $q2 = $database->query($q2);
    for($j = 0; $j < mysql_num_rows($q2); $j++)
      {
	$xml->push("Stat" . $j);
	$statId = mysql_result($q2, $j, "stat");
	$q3 = 'SELECT * FROM stats WHERE id = "' . $statId . '"';
	$q3 = $database->query($q3);
	if(mysql_num_rows($q3) > 0)
	  {
	    // store the analysis data
	    $time = mysql_result($q3, 0, "time");
	    $count = mysql_result($q3, 0, "count");
	    $goodness = mysql_result($q3, 0, "goodness");
	    $englishProficiency = mysql_result($q3, 0, "englishProficiency");
	    $xml->element("time", $time);
	    $xml->element("count", $count);
	    $xml->element("goodness", $goodness);
	    $xml->element("englishProfociency", $englishProficiency);
	  }

	$xml->pop();
      }
    
    $xml->pop();
    $xml->pop();
  }  

$xml->pop();

$xml->pop();
echo $xml->getXML();
  
// XML Writer class                                                                                            
class cXmlWriter {
  var $xml;
  var $indent;
  var $stack = array();
  function XmlWriter($indent = '  ') {
    $this->indent = $indent;
    $this->xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
  }
  function _indent() {
    for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
      $this->xml .= $this->indent;
    }
  }
  function push($element, $attributes = array()) {
    $this->_indent();
    $this->xml .= '<'.$element;
    foreach ($attributes as $key => $value) {
      $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
    }
    $this->xml .= ">\n";
    $this->stack[] = $element;
  }
  function element($element, $content, $attributes = array()) {
    $this->_indent();
    $this->xml .= '<'.$element;
    foreach ($attributes as $key => $value) {
      $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
    }
    $this->xml .= '>'.htmlentities($content).'</'.$element.'>'."\n";
  }
  function emptyelement($element, $attributes = array()) {
    $this->_indent();
    $this->xml .= '<'.$element;
    foreach ($attributes as $key => $value) {
      $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
    }
    $this->xml .= " />\n";
  }
  function pop() {
    $element = array_pop($this->stack);
    $this->_indent();
    $this->xml .= "</$element>\n";
  }
  function getXml() {
    return $this->xml;
  }
}


?>