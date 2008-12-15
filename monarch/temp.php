<?php
	// echo file_get_contents('http://www.threadless.com/blogs/blogs');		
	
	$fh = fopen('http://www.threadless.com/blogs/blogs', 'r');
	while($theData = fread($fh, 9999))
		echo $theData;
	fclose($fh);
	

?>

