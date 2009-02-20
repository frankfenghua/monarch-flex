<?php

/*
<div class="comment-content">
    			
    			<p>She&#8217;s doing a great job.  Keep it up Mrs. Obama.</p>
          <cite>&#8212; ovwong</cite>
        </div>
*/

	$html = file_get_contents('http://thecaucus.blogs.nytimes.com/2009/02/20/the-first-lady-lauds-transportation-workers/');

	preg_match_all('/<li class="print"><a href="([^"]+)"/', $html, $result);

	print_r($html);


	print_r($result);
?>

