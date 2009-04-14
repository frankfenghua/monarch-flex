<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

require_once('database/Database.php');

$email = $_GET['email'];
$password = $_GET['password'];

$database = new Database('master');

$q = 'SELECT id 
      FROM users 
      WHERE username = "' . $email . '"
      AND password = password("' . $password . '")';

$q = $database->query($q);

if(mysql_num_rows($q) == 0)
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
     $q = mysql_fetch_array($q);
     $_SESSION['userId'] = $q['id'];

     $realName = 'SELECT realName 
                  FROM users 
                  WHERE username = "' . $email . '"
                  AND password = password("' . $password . '")';
     
     $realName = $database->query($realName);
     $realName = mysql_fetch_array($realName);

     $xml = new cXmlWriter();
     $xml->push("LoggedInAccountInformation");
     // store the user account information
     $xml->push("UserAccountInformation");
     $xml->element("fullName", $realName[0]);
     $xml->element("email", $email);
     $xml->element("password", $password);
     $xml->element("userId", $q[0]);
     $xml->pop();
     // store the user's community group information along with the websites
     // and keywords
     $xml->push("CommunityGroups");
         $query = 'SELECT * FROM communities WHERE user = "' . $q[0] . '"';
         $result = $database->query($query);
	 $num_rows = mysql_num_rows($result);
	 $xml->element("NumberOfCommunityGroups", $num_rows);
	 for($i = 0; $i < $num_rows; $i++)
	   {
             $xml->push("CommunityGroup".$i);
	        $commId = mysql_result($result, $i, "id"); 
                $commName = mysql_result($result, $i, "name");
		$time = mysql_result($result, $i, "created");
		$xml->element("communityGroupId", $commId);
		$xml->element("communityGroupName", $commName);
		$xml->element("communityGroupCreatedTime", $time);
		
		$xml->push("CommunityGroup".$i."Websites");
		$websiteQuery = 'SELECT * FROM websites WHERE community = "' . $commId . '"';
		$websiteResult = $database->query($websiteQuery);
		$websiteNum_rows = mysql_num_rows($websiteResult);
		$xml->element("NumberOfWebsites", $websiteNum_rows);
		for($j = 0; $j < $websiteNum_rows; $j++)
		  {
		    $xml->push("Website".$j);
		       $websiteId = mysql_result($websiteResult, $j, "id");
		       $websiteName = mysql_result($websiteResult, $j, "name");
		       $websiteType = mysql_result($websiteResult, $j, "type");
		       $websiteCreatedTime = mysql_result($websiteResult, $j, "created");
		       $xml->element("websiteId", $websiteId);
		       $xml->element("websiteName", $websiteName);
		       $xml->element("websiteType", $websiteType);
		       $xml->element("websiteCreatedTime", $websiteCreatedTime);
		    $xml->pop();
		    }
		  $xml->pop();

		  $xml->push("CommunityGroup".$i."Keywords");
		  $keywordQuery = 'SELECT * FROM allowedkeywords WHERE community = "' . $commId . '"';
		  $keywordResult = $database->query($keywordQuery);
		  $keywordNum_rows = mysql_num_rows($keywordResult);
		  $xml->element("NumberOfKeywords", $keywordNum_rows);
		  for($j = 0; $j < $keywordNum_rows; $j++)
		    {
		      $xml->push("Keyword".$j);
		          $keywordId = mysql_result($keywordResult, $j, "id");
			  $keywordName = mysql_result($keywordResult, $j, "word");
			  $xml->element("keywordId", $keywordId);
			  $xml->element("keywordName", $keywordName);
		      $xml->pop();
		    }
		  $xml->pop();
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
