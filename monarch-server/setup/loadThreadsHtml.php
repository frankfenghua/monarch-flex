<?php

// title:  loadThreadsHtml.php
// author: Ryan Lin
// date:   10/17/08
// about:  Given a URL, which should be a page of threads, fetches its HTML.
// FIX:    check for loading of malicious JS scripts (it doesn't mean if a file
//         doesn't have a JS extension, then it isn't JS text inside that file).
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$html = @file_get_contents($_GET['url']) or die('<span class="error">Failed to download page.</a>');
	
	echo '<textarea>' . $html . '</textarea>';
	
	$fh = fopen('threadlessThreads.html', 'w') or die("can't open file");
	fwrite($fh, $html);
	fclose($fh);

?>