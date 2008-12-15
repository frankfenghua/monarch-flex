<?php

// title:  matchRegex.php
// author: Ryan Lin
// date:   10/17/08
// about:  Matches a regex against HTML and returns the first occurence that is
//         matched.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	if($_GET['regex'] == '')
		die('<span class="error">You did not enter any regex.</span>');
	
	if($_GET['file'] == 'threads')
		$file = 'threadlessThreads.html';
	else
		$file = 'threadlessPosts.html';

	preg_match_all(urldecode($_GET['regex']), file_get_contents($file), $match)
		or die('<span class="error">Could not match anything.</span>');
	
	echo strip_tags($match[1][0]);

?>