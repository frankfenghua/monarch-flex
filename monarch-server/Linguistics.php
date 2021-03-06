<?php

// TITLE:  Linguistics.php
// TYPE:   Class
// AUTHOR: Ryan Lin, Andrew Spencer
// DATE:   12/03/2008
// ABOUT:  Various ways to gauge the importance of a speaker and the positivity
//         or negativity which she speaks of a keyword. 
// ================================================================================

require_once('database/Database.php');
require_once('constants.php');
require_once('BinarySearch.php');

class Linguistics
{

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $database;          // used for accessing the linguistic tables
	private $badWords;          // BinarySearch object of negative words (linguistic analysis)
	private $goodWords;         // BinarySearch object of positive words (linguistic analysis)
	private $englishDictionary; // BinarySearch object of all the words in the english dictionary
	private $amplifiers;        // BinarySearch object of words that amplify the effect of an adjective (very, really, ...)
	private $inverters;         // BinarySearch object of words that invert the effect of an adjective (not, hardly, should be, could have been, ...) 
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// PUBLIC FUNCTIONS ...............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// ============================================================================
	// Linguistics
	//    args:  none
	//    ret:   none
	//    about: Constructor. Loads up all our word lists. 
	// ----------------------------------------------------------------------------
	public function Linguistics()
	{
		$this->database = new Database('master');
		
		$this->englishDictionary = $this->loadEnglishDictionary();
		
		// load adjectives and adverbs
		$this->goodWords  = $this->loadWordList('goodwords');
		$this->badWords   = $this->loadWordList('badwords');
		$this->inverters  = $this->loadWordList('inverters');
		$this->amplifiers = $this->loadWordList('amplifiers');
	}
	
	// ============================================================================
	// englishProficiency
	//    args:  string - a block of text (can contain HTML)
	//    ret:   double - [0.0 - 1.0]
	//    about: Checks the author's ability to capitalize, spell, and punctuate.
	//           1.0 means he's perfect, 0.0 means he's not even speaking English.
	// ----------------------------------------------------------------------------
	public function englishProficiency($bodyHtml)
	{
		$text = strip_tags($bodyHtml);
	
		// average
		return (
			$this->capitalization($text) +
			$this->spelling($text) + 
			$this->punctuation($text)
		) / 3;
	}
	
	// ========================================================================
	// cleanBody
	//    args:  string - can contain HTML.
	//    ret:   array of strings - lowercased and without punctuation. May 
	//                              contain URL's if the argument came with 
	//                              plain textual URL's (not anchors / images).
	//    about: Chunks all words into an array. Does cleaning necessary for 
	//           correct evaluation by the goodnessByIndex function.
	//    FIX:   only the first part of a hyphenated word will be added to the
	//           returned array. For example: "based" will not be included in
	//           "water-based".
	// ------------------------------------------------------------------------
	public function cleanBody($bodyHtml)
	{
		$body = strip_tags($bodyHtml);
	
		preg_match_all('/[^\s\n]+/', $body, $bodyWords);
		$bodyArray = $bodyWords[0];

		for($i = 0; $i < sizeof($bodyArray); $i++)
		{
			// case insensitivity
			$bodyArray[$i] = strtolower($bodyArray[$i]);			
			
			preg_match_all('#' . REGEX_SANDWICHED_PERIOD . '#', $bodyArray[$i], $itIsALink);
			
			// only remove punctuation if it is not a link because links have a ton of punctuation that we need to retain
			if(sizeof($itIsALink[0]) == 0)
			{
				preg_match_all('#\W*([' . REGEX_ENGLISH_WORD . ']+)\W*#', $bodyArray[$i], $noPunctuation);
				$bodyArray[$i] = $noPunctuation[1][0];
			}
		}
		
		return $bodyArray;
	}
	
	// ========================================================================
	// goodness
	//    args:  * string - the word which you want to know the rating of. Can
	//                      be a keyword or link.
	//           * string - the body of text that the word belongs to. 
	//    ret:   int - the magnitude of sign of this number tell how good (+)
	//                 or how bad (-) this word is spoken about. Returns 0.0 if
	//                 keyword is not even found in the body.
	//    about: A rating that tells you if the keyword or full URL is spoken 
	//           of positively or negatively. Counts ALL occurrences of the 
	//           keyword in the body.  If you just want to calculate the 
	//           goodness for just one occurrence, use goodnessByIndex instead.
	//           This function is just for testing - it's not meant for the 
	//           post processor. 
	// ------------------------------------------------------------------------
	public function goodness($keyword, $bodyHtml)
	{
		// make lowercase so == can be used correctly
		$keyword = strtolower($keyword);
		
		$bodyArray = $this->cleanBody($bodyHtml);
		
		$finalScore = 0;
	
		// find locations of the word in the body
		$keywordLocations = array_keys($bodyArray, $keyword);

		foreach($keywordLocations as $locationKeyword)
			$finalScore += $this->goodnessByIndex($locationKeyword, $bodyArray);
		
		return $finalScore;
	}
	
	// ========================================================================
	// goodness
	//    args:  int - the index of the occurrence of the keyword or link 
	//                 within the body array.
	//           array of strings - the body of text where the keyword or link
	//                              was found. Must be pre-cleaned of
	//                              punctuation and empty strings. 
	//    ret:   int - the magnitude of sign of this number tell how good (+)
	//                 or how bad (-) this word is spoken about. Returns 0.0 if
	//                 keyword is not even found in the body.
	//    about: Gives the goodness rating for a particular occurrence of a 
	//           keyword or link within the body.
	//    FIX:   * "like" is not necessarily a positive word because it could 
	//             be used as a synonym of "similar" instead of "love".
	//           * bad running time
	//           * not normalized to [0.0 - 1.0] range
	//           * "very" and "so" does not have to precede adjective. Ex: I like Adobe very much.
	public function goodnessByIndex($locationKeyword, $bodyArray)
	{	
		// Do not search the entire body for adjectives; rather only search around a vicinity of the keyword
		// This is used to cut down on processing time and also increase accuracy of goodness
		$lastIndexAdjective  = $locationKeyword + LING_KEYWORD_VICINITY;
		$firstIndexAdjective = $locationKeyword - LING_KEYWORD_VICINITY;
		
		if($firstIndexAdjective < 0)
			$firstIndexAdjective = 0;
		
		$locationAdjective = $firstIndexAdjective;
		$finalScore = 0;

		// scan through the whole body
		foreach($bodyArray as $adjective)
		{
			// done checking vicinity keyword
			if($locationAdjective > $lastIndexAdjective)
				return $finalScore;
		
			// can't use keyword itself as an adjective
			if($locationAdjective == $locationKeyword)
			{
				$locationAdjective++;
				continue;
			}

			// the adjective is preceded by an amplifier
			if($locationAdjective > 0 && $this->amplifiers->inArray($bodyArray[$locationAdjective - 1]))
				$severity = 2;
			else
				$severity = 1;
		
			$rating = 1 / abs($locationAdjective - $locationKeyword) * $severity;

			// goodness of a word is inversely proportional to it's distance from a good word
			if($this->goodWords->inArray($adjective))
			{
				// an inverter in front of a good word makes it bad.
				if($this->isInverted($bodyArray, $locationAdjective))
				{
					$finalScore -= $rating;
					
					if(DEBUG_GOODNESS)
						$this->debugGoodness($bodyArray[$locationKeyword], $adjective, -$rating);
				}
				else
				{
					$finalScore += $rating;
					
					if(DEBUG_GOODNESS)
						$this->debugGoodness($bodyArray[$locationKeyword], $adjective, $rating);
				}
			}
			
			// badness of a word is inversely proportional to it's distance from a bad word
			if($this->badWords->inArray($adjective))
			{	
				// an inverter in front of a bad word makes it good
				if($this->isInverted($bodyArray, $locationAdjective))	
				{		
					$finalScore += $rating;
					
					if(DEBUG_GOODNESS)
						$this->debugGoodness($bodyArray[$locationKeyword], $adjective, $rating);
				}
				else
				{
					$finalScore -= $rating;
					
					if(DEBUG_GOODNESS)
						$this->debugGoodness($bodyArray[$locationKeyword], $adjective, -$rating);
				}
			}
			
			$locationAdjective++;
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
	// ----------------------------------------------------------------------------
	public function capitalization($text)
	{
		preg_match_all('#[' . REGEX_SENTENCE_WO_PUNCT . ']+#', $text, $allSentences);

		// check if 1st char in each sentence is lowercase (or punctuation / numeric) and mark that as a mistake
		foreach($allSentences[0] as $sentence)
		{
			$firstCharInSentence = substr(trim($sentence), 0, 1);
			
			preg_match_all('#[' . REGEX_INPROPER_FIRST_CHAR_OF_WORD . ']#', $firstCharInSentence, $lowercasedStarter);
			
			if(sizeof($lowercasedStarter[0]) == 1)
				$numMistakes++;
		}

		$numSentences = sizeof($allSentences[0]);
		
		if($numSentences == 0)
			return 0;
		else
			return 1 - ($numMistakes / $numSentences);
	}

	// ============================================================================
	// spelling
	//    args:  string - a block of text
	//    ret:   double - [0.0 - 1.0]
	//    about: Checks how well this person's spelling is. Tells you the 
	//           percentage of correctly spelled words.
	//    FIX:   Does not check for phrases such as "a cappella"
	// ----------------------------------------------------------------------------
	public function spelling($text)
	{
		// this regex avoids matching links (recall that links analysis also requires calling englishProficiency)
		preg_match_all('#[' . REGEX_ENGLISH_WORD . ']+#', $text, $texts);
		$text = $texts[0];
		
		if(sizeof($text) == 0)
			return 0;
		
		foreach($text as $word)
		{
			if($this->englishDictionary->inArray(strtolower($word)))
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
	
	// ============================================================================
	// loadWordList
	//    args:  string - "inverters" | "amplifiers" | "goodwords" | "badwords"
	//    ret:   BinarySearch object
	//    about: Load a subset of English words. Each type of set has a different 
	//           effect on meaning of a sentence.
	// ----------------------------------------------------------------------------
	private function loadWordList($type)
	{
		$q = $this->database->query('SELECT word FROM ' . $type);
		
		while($row = mysql_fetch_array($q))
			$list[] = $row['word'];
			
		return new BinarySearch($list);
	}
	
	// ============================================================================
	// loadEnglishDictionary
	//    args:  none
	//    ret:   BinarySearch object
	//    about: Loads all English dictionary words in lowercased form.  Does not 
	//           include proper nouns.
	// ----------------------------------------------------------------------------
	private function loadEnglishDictionary()
	{
		$dictionary = @fopen("englishDictionary/englishDictionary.txt", "r");
		
		if(!$dictionary)
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
			die('Linguistics->loadWordList: english dictionary file could not be opened.');
		
		return new BinarySearch($englishDictionary);
	}
	
	// ============================================================================
	// isInverted
	//    args:  * array of strings
	//           * the index of an adjective in this array of strings
	//    ret:   boolean
	//    about: Check to see if the adjective is inverted. For example, the 
	//           meaning of the adjective "fair" is inverted in "hardly ever fair".
	//           Currently hardcoded to only support 3-word phrase inverters. 
	// ----------------------------------------------------------------------------
	private function isInverted($bodyArray, $locationAdjective)
	{
		// previous word is an inverter ("not")
		if($locationAdjective > 0)
		{
			if($this->inverters->inArray($bodyArray[$locationAdjective - 1]))
				return true;
		}
		
		// previous 2 words is an inverter conjunction ("hardly ever")
		if($locationAdjective > 1)
		{
			if($this->inverters->inArray($bodyArray[$locationAdjective - 2] . ' ' . $bodyArray[$locationAdjective - 1]))
				return true;
		}
		
		// previous 3 words is a inverter conjunction ("by no means")
		if($locationAdjective > 2)
		{
			if($this->inverters->inArray($bodyArray[$locationAdjective - 3] . ' ' . $bodyArray[$locationAdjective - 2] . ' ' . $bodyArray[$locationAdjective - 1]))
				return true;
		}
		
		return false;
	}
	
	// ============================================================================
	// debugGoodness
	//    args:  * string - word being affected by an adjective
	//           * string - adjective that's affecting the keyword
	//           * float (anything but 0.0)
	//    ret:   void
	//    about: Prints out how much an adjective increased or decreased the score
	//           of a keyword.
	// ----------------------------------------------------------------------------
	private function debugGoodness($keyword, $adjective, $rating)
	{
		if($rating < 0)
		{
			$color = 'red';
			$affect = 'decreased';
		}
		else 
		{
			$color = 'green';
			$affect = 'increased';
		}
	
		printf('<font color="%s"><h5><i>%s</i> (after inversion/amplification) %s the rating of %s by %f</h5></font>', 
			$color, $adjective, $affect, $keyword, $rating);
	}

}

?>
