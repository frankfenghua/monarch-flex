
<?php
	
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
