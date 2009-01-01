<?php
	include_once('Linguistics.php');

	$lin = new Linguistics();
	
	$body = 'http://www.yahoo.com i like';
	
	echo strip_tags($body);
?>
