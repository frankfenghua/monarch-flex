<?php

error_reporting(E_ALL);
ini_set('display_errors','1');

	
	$body = 'i suck www.threadless.com/whatever hello
		asdfa threadless.com asdfasd
		www.threadless.com/whatever
		http://www.threadless.com/whatever
		http://threadles.com/whatever';
		
	$body = 'a href="http://www.threadless.com" 
	img src="http://www.threadless.com" 
	img src="http://www.sub.threadless.com/asdfawef" 
	img src="sub.threadless.com/" 
	img src="http://sub.threadless.com/wa" ';
	
	$body = 'i like <a title="gay" href="http://www.threadless.com/submission/64813/The_Dreaming_Tree?streetteam=CallMeSteven" title="fuck">this link</a> i hate <img  onclick="alert()" src="www.yahoo.com" onmouseout="asfd" /> cause it';
	
	$body = 'One of the themes for the next issue is Beloved, and it would be super fucking awesome to win, although I really don\'t have a chance in hell.  It would be really cool of you if you\'d vote for me, though.  I\'d give you lots of cool points.<br />
<br />
<a href="http://www.jpgmag.com/photos/1171002"><img src="http://photos.jpgmag.com/1171002_90488_4329d3d61a_m.jpg" /></a><br>';
	
	$body = preg_replace('#<a.*href[ ]*=[ ]*"#i', '', $body);
	$body = preg_replace('#<img.*src[ ]*=[ ]*"#i', '', $body);
	$body = preg_replace('#"[^>]*>#', ' ', $body);
	$body = strip_tags($body);
	$body = preg_replace('/[\s]+/', ' ', $body);
	$body = trim($body);
	$body = explode(' ', $body);
	
	print_r($body);
	



		

	

?>
