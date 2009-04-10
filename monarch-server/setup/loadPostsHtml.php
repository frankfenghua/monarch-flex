<?php

// title:  loadPostsHtml.php
// author: Ryan Lin
// date:   10/19/08
// about:  Given a URL, which should be a single thread, fetches its HTML. Also
//         simultaneously does regex matching. I couldn't figure out a way to 
//         call AJAX twice, so that's why I did it.
// FIX:    check for loading of malicious JS scripts (it doesn't mean if a file
//         doesn't have a JS extension, then it isn't JS text inside that file).
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	preg_match_all(urldecode($_GET['regex']), file_get_contents('threadlessThreads.html'), $match)
		or die('<span class="error">Could not match anything.</span>');
	
	$fh = fopen('threadlessPosts.html', 'w') or die("can't open file");
	fwrite($fh, file_get_contents('http://www.threadless.com/' . $match[1][0])); // FIX: hardcoded
	fclose($fh);
	
	echo $match[1][0];

?>