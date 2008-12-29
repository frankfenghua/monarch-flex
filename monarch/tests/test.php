<?php
 error_reporting(E_ALL ^ E_NOTICE);
 ini_set('display_errors','1');
 
 $url = "http://threadless.com/blogs/blogs";
 $ch = curl_init(); 

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0"); // impersonate browser
// curl_setopt($ch, CURLOPT_POST, 1);
$result = curl_exec($ch);
curl_close($ch);
echo $result;

?>
