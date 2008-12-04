<?php

// TITLE:  fileToDatabase.php
// TYPE:   Excutable
// AUTHOR: Ryan Lin
// DATE:   12/04/2008
// ABOUT:  Takes a file complete with one English word per line and inserts them
//         into the database.
// ================================================================================

require_once('../database/Database.php');

$database = new Database('master');

$dictionary = @fopen("englishDictionary.txt", "r");

if($dictionary) 
{
	echo 'dictionary file was opened successfully <br />';

	while(!feof($dictionary)) 
	{
		$word = mysql_real_escape_string(trim(strtolower(fgets($dictionary))));
	
		// do not insert blank words or duplicate words
		if($word != '' && mysql_num_rows($database->query('SELECT word FROM englishdictionary WHERE word = "' . $word . '"')) == 0)
		{
			$q = 'INSERT INTO englishdictionary (word) VALUES ("' . $word . '")';
			$database->query($q);
		}
	}
	
	fclose($dictionary);
	
	echo 'all words were inserted into the database';
}



?>