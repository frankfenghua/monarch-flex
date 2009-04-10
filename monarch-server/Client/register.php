<?php

require_once('../database/Database.php');

$fullName = $_GET['fullName'];
$email =  $_GET['email'];
$password =  $_GET['password'];

$database = new Database('master');

$q = 'SELECT id FROM users WHERE username = "' . $email . '"';

if(mysql_num_rows($database->query($q)) > 0)
  {
    $xml = new cXmlWriter();
    $xml->push("LoggedInAccountInformation");
    $xml->push("UserAccountInformation");
    $xml->pop();
    $xml->pop();

    echo $xml->getXML();
  }
 else
   {
     $q = 'INSERT INTO users (username, realName, created, password) VALUES( "' . $email . '", 
           "' . $fullName . '", 
           "' . time() . '", 
           password("' . $password . '"))';
     $database->query($q);
     $_SESSION['userId'] = mysql_insert_id();

     $xml = new cXmlWriter();
     $xml->push("LoggedInAccountInformation");
     // store the user account information                                                                     
     $xml->push("UserAccountInformation");
     $xml->element("fullName", $fullName);
     $xml->element("email", $email);
     $xml->element("password", $password);
     $xml->element("userId", mysql_insert_id());
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
