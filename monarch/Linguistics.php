<?php

// TITLE:  Linguistics.php
// TYPE:   Class
// AUTHOR: Ryan Lin, Andrew Spencer
// DATE:   12/03/2008
// ABOUT:  Various ways to gauge the importance of a speaker. All functions take
//         normal text as you would find in a book. HTML and other formatting
//         be removed before using any of these functions.
// ================================================================================

require_once('database/Database.php');
require_once('constants.php');

class Linguistics
{

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $badWords;          // array of negative words (linguistic analysis)
	private $goodWords;         // array of positive words (linguistic analysis)
	private $englishDictionary; // array of all the words in the english dictionary
	private $database;          // used for accessing the linguistic tables
	private $inverters;         // array of words that invert the effect of an adjective
	private $amplifiers;        // array of words that amplify the effect of an adjective
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// PUBLIC FUNCTIONS ...............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// ============================================================================
	// Linguistics
	//    args:  none
	//    ret:   none
	//    about: Constructor. Loads up the good/bad words. Will not load the entire
	//           English dictionary because it's 7MB and that would take forever.
	//    FIX:   shitload of good amplifiers and inverters that we should use, but
	//           currently we don't support multi-word modifers. Use thesaurus.com
	//           to find more.
	//
	//           no more than, not a bit, not at all, not by much, not likely, 
	//           not markedly, not measurably, not much, not notably, not 
	//           noticeably, not often, not quite, no way, once in a blue moon.
	// 
	//           Horrible balance between inverters and amplifiers at the moment. 
	// ----------------------------------------------------------------------------
	public function Linguistics()
	{
		$this->database = new Database('master');
		
		// load english dictionary 
		$dictionary = @fopen("englishDictionary/englishDictionary.txt", "r");
		
		if(!$dictionary)
			$dictionary = @fopen("../englishDictionary/englishDictionary.txt", "r");
		
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
		
		// modifiers	
		$this->inverters = explode(' ', 'not don\'t hardly neither nought barely faintly imperceptibly infrequently 
			rarely scantly seldom sparsely');
			
		$this->amplifiers = explode(' ', 'very so much really absolutely acutely amply astonishingly awfully certainly 
			considerably dearly decidedly deeply eminently emphatically exaggeratedly exceedingly excessively extensively 
			extraordinarily extremely greatly highly incredibly indispensably largely notably noticeably particularly 
			positively powerfully pressingly pretty prodigiously profoundly remarkably substantially superlatively 
			surpassingly surprisingly terribly truly uncommonly unusually vastly wonderfully');
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
	//           of positively or negatively. Count ALL occurrences of the 
	//           keyword in the body.  If you just want to calculate the 
	//           goodness for just one occurrence, use goodnessByIndex instead.
	//           This function is just for testing - it's not meant for the post 
	//           processor. 
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
	//           * do stemming and augmenting of goodWords.
	//           * bad running time
	//           * if $word belonged to goodWords or badWords, there is a 
	//             chance of division by zero.
	//           * not normalized to [0.0 - 1.0] range
	//           * "very" and "so" does not have to precede adjective. Ex: I like it very much.
	public function goodnessByIndex($locationKeyword, $bodyArray)
	{
		$locationAdjective = 0;
		$finalScore = 0;

		// scan through the whole body
		foreach($bodyArray as $adjective)
		{
			// can't use keyword itself as an adjective
			if($locationAdjective == $locationKeyword)
			{
				$locationAdjective++;
				continue;
			}

			// the adjective is preceded by an amplifier
			if($locationAdjective > 0 && in_array($bodyArray[$locationAdjective - 1], $this->amplifiers))
				$severity = 2;
			else
				$severity = 1;
		
			$rating = 1 / abs($locationAdjective - $locationKeyword) * $severity;

			// goodness of a word is inversely proportional to it's distance from a good word
			if(in_array($adjective, $this->goodWords))
			{
				// an inverter in front of a good word makes it bad.
				if($locationAdjective > 0 && in_array($bodyArray[$locationAdjective - 1], $this->inverters))
					$finalScore -= $rating;
				else
					$finalScore += $rating;
			}
			
			// badness of a word is inversely proportional to it's distance from a bad word
			if(in_array($adjective, $this->badWords))
			{	
				// an inverter in front of a bad word makes it good
				if($locationAdjective > 0 && in_array($bodyArray[$locationAdjective - 1], $this->inverters))				
					$finalScore += $rating;
				else
					$finalScore -= $rating;
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
	//    fix:   make sure passed in text has no html
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
	//    FIX:   * Does not check for multi-word words such as "a cappella"
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
