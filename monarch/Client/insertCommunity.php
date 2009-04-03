<?php

require_once('../database/Database.php');

$database = new Database('master');

$comName = $_GET['communityName'];
$userId = $_GET['userId'];

$query = 'SELECT id FROM communities WHERE name = "' . $comName . '"';

$query = $database->query($query);

if(mysql_num_rows($query) > 0)
  {
    echo "-1";
  }
 else
   {
     $xml = new cXmlWriter();
     $xml->push("NewlyInsertedCommunity");
     $xml->push("CommunityInformation");

     $time = time();
     $query = 'INSERT INTO communities (user, name, created)
              VALUES("' . $userId . '",
              "' . $comName . '",
              "' . $time . '")';
     $database->query($query);
     $communityId = mysql_insert_id();

     // store the community id and name
     $xml->element("communityGroupId", $communityId);
     $xml->element("communityGroupName", $comName);
     $xml->element("communityGroupCreatedTime", $time);
     $xml->pop();

     // store the community keywords
     $xml->push("CommunityGroupKeywords");

     $i = 1;
     while($_GET['keyWord' . $i])
       {
	 $xml->push("Keyword".($i - 1));

	 $keyword = $_GET['keyWord' . $i];
	 $query = 'INSERT INTO allowedkeywords (community, word)
                   VALUES("' . $communityId . '",
                   "' . $keyword . '")';

	 $database->query($query);
	 $i++;

	 $xml->element("keywordId", mysql_insert_id());
	 $xml->element("keywordName", $keyword);

	 $xml->pop();
       }

     $xml->pop();
     $xml->pop();

     echo $xml->getXML();
   }

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