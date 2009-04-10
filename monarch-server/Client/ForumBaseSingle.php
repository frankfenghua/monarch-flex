<?php
	
// hostname or ip of server (for local testing, localhost should work)
$dbServer='localhost';

// username and password to log onto db server
$dbUser='ryan';
$dbPass='adobe';

// grab the arguments passed from the HTTPService component
$array = Array($_GET["mArgsArray"]);
// grab the database name as the first argument in $array
$dbName=$array[0];

    $link = mysql_connect("$dbServer", "$dbUser", "$dbPass") or die("Could not connect");
    
    mysql_select_db("$dbName") or die("Could not select database");
	
	$xml = new cXmlWriter();

	$xml->push("ForumCommunitySingleDatabase");
	$xml->push("BasicAnalysis");

	// parse the users table for entries and store them in the XML
	$query = 'SELECT * FROM analysis';
	$result = mysql_query($query, $link);
	
	// get the number of rows for the table
	$num_rows = mysql_num_rows($result);

	// store the user data table data in the xml
	$xml->push("analysis");
	
	for($i = 0; $i < $num_rows; $i++)
	{
		$xml->push("Block".$i);
		if(mysql_result($result, $i, "type") == "id")
		{
			if(mysql_result($result, $i, "table") == "keywords")
			{
				$xml->element("type", "keywords");
				$xml->element("field", mysql_result($result, $i, "field"));
				
				$id = mysql_result($result, $i, "value");
				$xml->element("word", mysql_result(mysql_query("SELECT * FROM keywords WHERE id = $id", $link), 0, "word"));
				$xml->element("count", mysql_result(mysql_query("SELECT * FROM keywords WHERE id = $id", $link), 0, "count"));
			} 
			if(mysql_result($result, $i, "table") == "users")
			{
				$xml->element("type", "users");
				$xml->element("field", mysql_result($result, $i, "field"));

				$id = mysql_result($result, $i, "value");
				$xml->element("name", mysql_result(mysql_query("SELECT * FROM users WHERE id = $id", $link), 0, "name"));
				$xml->element("posts", mysql_result(mysql_query("SELECT * FROM users WHERE id = $id", $link), 0, "posts"));
			}
		}
		// id is scale value
		else
		{
			$xml->element("type", mysql_result($result, $i, "type"));
			$xml->element("field", mysql_result($result, $i, "field"));
			$xml->element("value", mysql_result($result, $i, "value"));
		}		

		$xml->pop();
	}
	
	$xml->pop();

	// parse the threads table for entries and store them in the XML
	/*$query = 'SELECT * FROM threads';
	$result = mysql_query($query, $link);

	// get the number of rows for the table
	$num_rows = mysql_num_rows($result);

	// store the threads data table data in the xml
	$xml->push("threads");

	for($i = 0; $i < $num_rows; $i++)
	{
		$xml->push("Thread".$i);
		$xml->element("title", mysql_result($result, $i, "title"));
		$xml->element("posts", mysql_result($result, $i, "posts"));
		$xml->element("views", mysql_result($result, $i, "views"));
		$xml->pop();
	}

	$xml->pop();
	*/

	$xml->pop();
	$xml->pop();
	
	echo $xml->getXML();

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
