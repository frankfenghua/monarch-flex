<?php

	require('BinarySearch.php');
	
	$arr = array('za', 'cd', 'ab', 'orp');
	
	$bin = new BinarySearch($arr, false);
	
	echo $bin->findIndex('aa');
	echo $bin->findIndex('zz');
	echo $bin->findIndex('cd');
	echo $bin->findIndex('za');
	echo $bin->findIndex('ab');
	
?>

