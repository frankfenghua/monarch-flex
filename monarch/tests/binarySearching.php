<?php

	require('../BinarySearch.php');
	
	// load up english dictionary
	$dictionary = @fopen("../englishDictionary/englishDictionary.txt", "r");
	
	if($dictionary) 
	{
		while(!feof($dictionary)) 
		{
			$word = trim(strtolower(fgets($dictionary)));
		
			if($word != '')
				$englishDictionary[] = $word;
		}
		
		fclose($dictionary);
	}
	else
		die('english dictionary file could not be opened.');
		
	// do binary searches on random words
	$binary = new BinarySearch($englishDictionary);
	
	$findThese = array('ass', 'fuck', 'zebra', 'shit', 'sucks', 'nonexistentword', 
		'horriblemisspeilling', 'christ', 'damn', 'dam', 'dude', 'zany', 'fork', 
		'process', 'whore', 'bin', 'lesbo', 'god', 'hemeroid', 'killer', 'kill',
		'animal', 'yellow', 'blue', 'green', 'greedy', 'algorithm', 'barfinyourface',
		'nigarrofigaro');
		
	foreach($findThese as $findThis)
		printf('<h3>index where <font color="red">%s</font> is located: <font color="green">%d</font></h3>', $findThis, $binary->findIndex($findThis));

?>