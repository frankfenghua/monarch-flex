<?php
	require('database/Database.php');
	
	$inverters = explode(', ', 'not, don\'t, hardly, neither, nought, barely, faintly, imperceptibly, infrequently, rarely, scantly, seldom, sparsely, by no means, not a bit, not at all, not likely, not markedly, not measurably, not much, not notably, not noticeably, not often, not quite, no way, never, hardly ever, in no way');
			
		$amplifiers = explode(', ', 'very, so, much, really, absolutely, acutely, amply, astonishingly, awfully, certainly, considerably, dearly, decidedly, deeply, eminently, emphatically, exaggeratedly, exceedingly, excessively, extensively, extraordinarily, extremely, greatly, highly, incredibly, indispensably, largely, notably, noticeably, particularly, positively, powerfully, pressingly, pretty, prodigiously, profoundly, remarkably, substantially, superlatively, surpassingly, surprisingly, terribly, truly, uncommonly, unusually, vastly, wonderfully, always, dreadfully, exceptionally, extra, most');
		
		
		$db = new Database('master');
		
		foreach($inverters as $inverter)
			$db->query('INSERT INTO inverters (word) VALUES ("' . $inverter . '")');
		
		foreach($amplifiers as $amplifier)
			$db->query('INSERT INTO amplifiers (word) VALUES ("' . $amplifier . '")');
?>
