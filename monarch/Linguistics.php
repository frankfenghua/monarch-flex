<?php

// TITLE:  Linguistics.php
// TYPE:   Class
// AUTHOR: Ryan Lin
// DATE:   12/03/2008
// ABOUT:  Various ways to gauge the importance of a speaker.
// ================================================================================

require_once('database/Database.php');

class Linguistics
{

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $badWords;          // array of negative words (linguistic analysis)
	private $goodWords;         // array of positive words (linguistic analysis)
	private $englishDictionary; // array of all the words in the english dictionary
	private $database;          // used for accessing the linguistic tables
	private $lowercaseLetters;  // a through z
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// PUBLIC FUNCTIONS ...............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	// ============================================================================
	// Linguistics
	//    args:  none
	//    ret:   none
	//    about: Constructor. Loads up the good/bad words. Will not load the entire
	//           English dictionary because it's 7MB and that would take forever.
	// ----------------------------------------------------------------------------
	public function Linguistics()
	{
		$this->database = new Database('master');
		
		// load english dictionary
		$dictionary = @fopen("/usr/share/dict/linux.words", "r");
		
		if($dictionary) 
		{
			while(!feof($dictionary)) 
			{
				$word = trim(strtolower(fgets($dictionary)));
			
				if($word != '')
					$this->englishDictionary[] = $word;
			}
			
			fclose($dictionary);
		}
		else
			die('english dictionary file could not be opened.');
		
		// load good words	
		$q = 'SELECT word FROM goodwords';
		
		$q = $this->database->query($q);
		
		while($row = mysql_fetch_array($q))
			$this->goodWords[] = $row['word'];
		
		// load bad words
		$q = 'SELECT word FROM badwords';
		
		$q = $this->database->query($q);
		
		while($row = mysql_fetch_array($q))
			$this->badWords[] = $row['word'];
			
		// load all lowercase letters
		$this->lowercaseLetters = explode(' ', 'a b c d e f g h i j k l m n o p q r s t u v w x y z');
	}
	
	
	// ============================================================================
	// englishProficiency
	//    args:  string - a block of text
	//    ret:   double - [0.0 - 1.0]
	//    about: Checks the author's ability to capitalize, spell, and punctuate.
	//           1.0 means he's perfect, 0.0 means he's not even speaking English.
	// ----------------------------------------------------------------------------
	public function englishProficiency($text)
	{
		// average
		return (
			$this->capitalization($text) +
			$this->spelling($text) + 
			$this->punctuation($text)
		) / 3;
	}
	
	// ========================================================================
	// goodness
	//    args:  * string - the word which you want to know the rating of
	//           * strings - the body of text that the word belongs to
	//    ret:   int - the magnitude of sign of this number tell how good (+)
	//                 or how bad (-) this word is spoken about. Returns 0.0 if
	//                 keyword is not even found in the body.
	//    about: Removes all symbols or words that could confuse PHP's 
	//           strtotime() and returns the Unix timestamp.
	//    fix:   * "like" is not necessarily a positive word because it could 
	//             be used as a synonym of "similar" instead of "love".
	//           * do stemming and augmenting of goodWords.
	//           * bad running time
	//           * if $word belonged to goodWords or badWords, there is a 
	//             chance of division by zero.
	//           * not normalized to [0.0 - 1.0] range
	//           * "very" and "so" does not have to precede adjective. Ex: I like it very much.
	// ------------------------------------------------------------------------
	// public function goodness($keyword, $body)
	public function goodness($keyword, $body)
	{
		// make everything lowercase so == can be used correctly
		$keyword = strtolower($keyword);

		// $body = explode($body, ' ');
		preg_match_all('/[a-zA-Z]+/', $body, $body_words);
		$body = $body_words[0];

		for($i = 0; $i < sizeof($body); $i++)
			$body[$i] = strtolower($body[$i]);
		
		$finalScore = 0;
	
		// find location of the word in the body
		// $locationKeyword = array_search($keyword, $body);
		$keywordLocations = array_keys($body, $keyword);
		
		// $locationAdjective = 0;

		foreach($keywordLocations as $locationKeyword)
		{
			$locationAdjective = 0;

		// scan through the whole body
		foreach($body as $adjective)
		{
			

			// can't use keyword itself as an adjective
			if($locationAdjective == $locationKeyword)
			{
				$locationAdjective++;
				continue;
			}

			// an adjective is amplified if preceded by "very" or "so"
			if($locationAdjective > 0 && ($body[$locationAdjective - 1] == 'very' || $body[$locationAdjective - 1] == 'so'))
				$severity = 2;
			else
				$severity = 1;
		
			$rating = 1 / abs($locationAdjective - $locationKeyword) * $severity;

			// goodness of a word is inversely proportional to it's distance from a good word
			if(in_array($adjective, $this->goodWords))
			{
				// "not" makes a good word bad
				if($locationAdjective > 0 && $body[$locationAdjective - 1] != 'not' && $body[$locationAdjective - 1] != "don't")
					$finalScore += $rating;
				else
					$finalScore -= $rating;
			}
			
			// badness of a word is inversely proportional to it's distance from a bad word
			if(in_array($adjective, $this->badWords))
			{	
				// "not" makes a bad word good
				if($locationAdjective > 0 && ($body[$locationAdjective - 1] == 'not' || $body[$locationAdjective - 1] == "don't"))				
					$finalScore += $rating;
				else
					$finalScore -= $rating;
			}
			
			$locationAdjective++;
		}

		}
		
		return $finalScore;
	}
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// PRIVATE FUNCTIONS ..............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// ============================================================================
	// capitalization
	//    args:  string - a block of text
	//    ret:   double - [0.0 - 1.0]
	//    about: How well does this author capitalize the next letter after ending
	//           a sentence? Gives a percentage of the sentences that were 
	//           capitalized correctly.
	//    fix:   make sure passed in text has no html
	// ----------------------------------------------------------------------------
	public function capitalization($text)
	{
		preg_match_all('#[^\?\.!]+#', $text, $allSentences);

		foreach($allSentences[0] as $sentence)
		{
			if(in_array(substr(trim($sentence), 0, 1), $this->lowercaseLetters))
				$numMistakes++;
		}

		$numSentences = sizeof($allSentences[0]);
		
		/*
		// preg_match_all('#[?\.!]+(?:<br>|<br />|<br/>|\n)*([a-zA-Z0-9])#', $text, $startingCharacters);
		preg_match_all('#[\?\.!]+(?:\n)*([a-zA-Z0-9])#', $text, $startingCharacters);
		
		
		
		foreach($startingCharacters as $startCharacter)
		{
			if(!is_numeric($startCharacter) && in_array($startCharacter, $this->lowercaseLetters))
				$numMistakes++;
		}
		
		// check that first word of the first sentence is capitalized.
		if(!in_array(substr($text, 0, 1), $this->lowercaseLetters))
			$numMistakes++;
		*/
		
		return 1 - ($numMistakes / $numSentences);
	}

	// ============================================================================
	// spelling
	//    args:  string - a block of text
	//    ret:   double - [0.0 - 1.0]
	//    about: Checks how well this person's spelling is. Tells you the 
	//           percentage of correctly spelled words.
	//    FIX:   Does not check for multi-word words such as "a cappella"
	// ----------------------------------------------------------------------------
	public function spelling($text)
	{
		preg_match_all('/[a-zA-Z]+/', $text, $texts);
		$text = $texts[0];
		
		foreach($text as $word)
		{
			if(in_array(strtolower($word), $this->englishDictionary))
				$numSpelledCorrect++;
		}
		
		return $numSpelledCorrect / sizeof($text);
	}
	
	// ============================================================================
	// punctuation
	//    args:  string - a block of text
	//    ret:   double - [0.0 - 1.0]
	//    about: Checks that single quotes, double quotes, and paretheses are 
	//           closed properly. Tells you the percentage of closed punctuation.
	//    FIX:   Does not check for proper nesting. Only checks for pairing.
	//           Does not account for smileys that use parentheses.
	//           There's more types of punctuation that can be done.
	// ----------------------------------------------------------------------------
	public function punctuation($text)
	{
		$numQuotes      = substr_count($text, '"') + substr_count($text, '&quot;');
		$numStartParens = substr_count($text, '(');
		$numEndParens   = substr_count($text, ')');
		
		if($numQuotes == 0)
		{
			$percentClosedQuotes = 1.0;
		}
		else	
		{
			if($numQuotes % 2 == 1)
			{
				$numUnclosedQuotes = 1;
				$numQuotePairs = ($numQuotes - 1) / 2;
			}
			else
			{
				$numUnclosedQuotes = 0;
				$numQuotePairs = $numQuotes / 2;
			}

			$percentClosedQuotes = 1 - ($numUnclosedQuotes / ($numQuotePairs + $numUnclosedQuotes));
		}
		
		if($numStartParens + $numEndParens == 0)
		{
			$percentClosedParens = 1.0;
		}
		else
		{	
			$numUnclosedParens   = abs($numStartParens - $numEndParens);
			$numParensPairs      = min($numStartParens, $numEndParens) / 2;
			$percentClosedParens = 1 - ($numUnclosedParens / ($numUnclosedParens + $numParensPairs));
		}
		
		// average
		return (($percentClosedParens + $percentClosedQuotes) / 2);
	}

}

?>
