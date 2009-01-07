<?php
/*
		<a class="lavendar" href="/profile/432502/ISABOA">ISABOA</a> on Aug 01 '07 at 6:27pm	
	*/

	$html = file_get_contents('http://www.threadless.com/profile/470607/wotto/blog/247841/percentage_Blowwwwg');
	
	preg_match_all('/<a.+href="(.+)">.+<\/a> on [A-Z][a-z]{2} [0-9]{2} \'[0-9]{2} at/', $html, $match);
	
	print_r($match);
?>
