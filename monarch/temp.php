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
	
	$body = 'i like <a title="gay" href="http://www.threadless.com/submission/64813/The_Dreaming_Tree?streetteam=CallMeSteven" title="fuck">this link</a> i hate <img  onclick="alert()" src="www.yahoo.com" onmouseout="asfd" /> cause it';
	
	$body = 'One of the themes for the next issue is Beloved, and it would be super fucking awesome to win, although I really don\'t have a chance in hell.  It would be really cool of you if you\'d vote for me, though.  I\'d give you lots of cool points.<br />
<br />
<a href="http://www.jpgmag.com/photos/1171002"><img src="http://photos.jpgmag.com/1171002_90488_4329d3d61a_m.jpg" /></a><br> fuck you';
	
	

	$body = preg_replace('#<a.*href[ ]*=[ ]*"#i', '', $body);
	$body = preg_replace('#<img.*src[ ]*=[ ]*"#i', '', $body);
	$body = preg_replace('#"[^>]*>#', ' ', $body);
	$body = strip_tags($body);
	$body = preg_replace('/[\s]+/', ' ', $body);
	$body = trim($body);

	echo '<h1>cleaned body </h1>' . $body;	

	$body = preg_replace('#(?:http://www\.|http://|www\.).+ #i', '', $body);

	echo '<h1>links removed </h1>' . $body;

	/*
	preg_match_all('/[^ ]+/', $body, $body_words);

		$body = $body_words[0];

		// case insensitivity and remove punctuation
		for($i = 0; $i < sizeof($body); $i++)
		{
			$body[$i] = strtolower($body[$i]);
			
			//preg_match_all('#\W*([a-z]+)#i', $body[$i], $noPunctuationBegin);

			//$body[$i] = $noPunctuationBegin[1][0];			
			
			preg_match_all('#[a-z0-9]\.[a-z0-9]#i', $body[$i], $itIsALink);
			
			if(sizeof($itIsALink[0]) == 0)
			{
				preg_match_all('#\W*([a-z]+)\W*#i', $body[$i], $noPunctuation);
				$body[$i] = $noPunctuation[1][0];
			}

		}
	*/
	
	print_r($body);
	



		

	

?>
