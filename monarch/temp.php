<?php



	
	$body = 'i suck www.threadless.com/whatever hello
		asdfa threadless.com asdfasd
		www.threadless.com/whatever
		http://www.threadless.com/whatever
		http://threadles.com/whatever';
		
	$body = 'a href="http://www.threadless.com" img src="http://www.threadless.com" ';
		
	$numLinks = preg_match_all('#(href|src)="([^"]+)"#i', $body, $links);	
	
	// $numLinks = preg_match_all('#(http://www\.)|(http://)|(www\.)#i', $body, $links);	
		
	// $numLinks = preg_match_all('#(http://)?(www\.)?(.+)#i', $body, $links);
	
	// $numLinks = preg_match_all('#(www.[^ ]+\.[a-z]{2,})[^a-z/]#i', $body, $links);
	
	// preg_match_all('#(http://)?(www\.)?(.*?\.[a-zA-Z]{3})#', $body, $links);
	
	print_r($links);
	
	// echo 'the time is: ' . time();
	

?>