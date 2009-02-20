<?php

	$html = file_get_contents('http://www.threadless.com/profile/470607/wotto/blog/247841/percentage_Blowwwwg/page,1');

	preg_match_all('/[\s]+<a class="pagea " href="(.*?)"/', $html, $result);

	print_r($html);


	print_r($result);
?>

