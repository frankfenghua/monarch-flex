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
		
	preg_match_all('#(href|src)="([^"]+)"#i', $body, $links);

	// print_r($links);	
		

	foreach($links[2] as $link)
	{
		$link = str_ireplace('http://', '', $link);
		$link = str_ireplace('www.', '', $link);

		print_r($link); echo '<br />';

		preg_match_all('#(?:[a-z]+\.)?([a-z0-9\-]+\.[a-z]{2,})#i', $link, $subdomains);	

		// preg_match_all('#([a-z0-9\-]+\.[a-z]{2,})#i', $link, $noSubdomains);	

	
		print_r($subdomains);
		// print_r($noSubdomains);

	}
	
	// echo 'the time is: ' . time();
	

?>
