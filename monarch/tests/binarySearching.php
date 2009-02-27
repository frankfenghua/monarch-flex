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
	
	sort($englishDictionary);
		
	// do binary searches on random words
	$binary = new BinarySearch($englishDictionary);
	
	$findThese = array('ass', 'fuck', 'zebra', 'shit', 'sucks', 'nonexistentword', 
		'horriblemisspeilling', 'christ', 'damn', 'dam', 'dude', 'zany', 'fork', 
		'process', 'whore', 'bin', 'lesbo', 'god', 'hemeroid', 'killer', 'kill',
		'animal', 'yellow', 'blue', 'green', 'greedy', 'algorithm', 'barfinyourface',
		'nigarrofigaro', 'hoe', 'hot', 'dog', 'mustard', 'ketchup', 'bong', 'puneet',
		'vera', 'adobe', 'robot', 'hardcoded', 'hard', 'soft', 'cunt', 'blows', 
		'hits', 'punches', 'punching', 'kick', 'kicker', 'kickers', 'kicks', 'kicked',
		'medicinal', 'marijuana', 'alcohol', 'abuse', 'triple', 'a', 'jail', 'slap',
		'womanizer', 'femenist', 'zeal', 'zoo', 'june', 'april');
		
	$startTime = microtime(true);	
	
	if($_GET['binary'] == 'true')
	{	
		foreach($findThese as $findThis)
			printf('<h3>index where <font color="red">%s</font> is located: <font color="green">%d</font></h3>', $findThis, $binary->findIndex($findThis));
	}
	else if($_GET['binary'] == 'false')
	{
		foreach($findThese as $findThis)
			printf('<h3>index where <font color="red">%s</font> is located: <font color="green">%d</font></h3>', $findThis, array_search($findThis, $englishDictionary));
	}
	else
		die('usage: binarySearching.php?binary=true || binarySearching.php?binary=false');

	echo "Execution time = ". (microtime(true) - $startTime)." sec";
?>
