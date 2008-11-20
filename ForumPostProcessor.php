<?php
  ////////////////////////////////////////////////////////////////////
  // ForumProcessor.php                                            
  // Adapted by Andrew Spencer from code written by Ryan Lin
  //  November 2008
  //  
  // Defines the class ForumProcessor which implements Processor;
  // 
  // 
  ////////////////////////////////////////////////////////////////////

require_once 'database/Database.php';
require_once 'Processor.php';

class ForumPostProcessor implements Processor {

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $database;     // connection to the master DB or specific community's DB
	private $domain;       // name of the community
	private $plugin;       // regular expressions for how to scrape this community
	private $delay;        // time to wait before traversing to another page
	private $timeStart;    // the time when we started scraping (used for time based statistics)
	private $allowedWords; // array of keywords which we are scraping for 
	private $badWords;     // array of negative words (linguistic analysis)
	private $goodWords;    // array of positive words (linguistic analysis)
  private $start_time;  // Time this processor was created

  public function ForumPostProcessor($domain) {
    $this->domain       = $domain;
    $this->database     = new Database('master');
    $this->allowedWords = $this->loadAllowedWords();
    $this->badWords     = $this->loadBadWords();
    $this->goodWords    = $this->loadGoodWords();
    $this->database     = new Database($domain);
    $this->plugin       = $this->loadPlugin();
    
    $this->timeStart    = time();
  }

  public function process($html) {
    // $this->dummy($html);
    $dummyThreadID = 1;
    $this->scrapePosts($dummyThreadID, $html);
  }

	// ========================================================================
	// scrapePosts
	//    args:  int - thread ID (MySQL)
	//           string - HREF of the thread
	//    ret:   void
	//    about: Goes into a particular thread. Scrapes all the replies of this
	//           thread and inserts stats into the database. 
	// ------------------------------------------------------------------------	
	private function scrapePosts($threadId, $html)
	{
		if($this->plugin['firstPostAuthor'] != NULL)
			preg_match_all($this->plugin['firstPostAuthor'], $html, $firstPostAuthor);
		
		if($this->plugin['firstPostTime'] != NULL)
			preg_match_all($this->plugin['firstPostTime'], $html, $firstPostTime);
		
		if($this->plugin['firstPostMessage'] != NULL)
			preg_match_all($this->plugin['firstPostMessage'], $html, $firstPostMessage);
		
		if($this->plugin['replyAuthor'] != NULL)
			preg_match_all($this->plugin['replyAuthor'], $html, $replyAuthors);
		
		if($this->plugin['replyTime'] != NULL)
			preg_match_all($this->plugin['replyTime'], $html, $replyTimes);
		
		if($this->plugin['replyMessage'] != NULL)
			preg_match_all($this->plugin['replyMessage'], $html, $replyMessages);
		
		// insert the starting post
		$this->insertPost($firstPostAuthor[1][0], $firstPostTime[1][0], $firstPostMessage[1][0], $threadId);
		
		// insert all the following replies
		for($i = 0; $i < sizeof($replyAuthors[1]); $i++)
			$this->insertPost($replyAuthors[1][$i], $replyTimes[1][$i], $replyMessages[1][$i], $threadId);
	}

	// ========================================================================
	// insertKeywords
	//    args:  string - message body text
	//           int - unix timestamp of the message
	//    ret:   void
	//    about: Inserts stats each word found in the message body. 
	//    fix:   * Why are spaces still being counted as a word?
	//           * Need to only store keywords that were said between this 
	//             session and the last session.
	// ------------------------------------------------------------------------	
  public function insertKeywords($html) {
    echo "Collecting Keywords";
    // remove punctuation and HTML
    $body = preg_replace('/[\s]+/', ' ', $html);
    $body = preg_replace('/<[^>]+>/', ' ', $html);
    
    preg_match_all('/[a-zA-Z]+/',$body,$body);
    
    foreach($body[0] as $word)
      {	
	// don't care about capitalization
	$word = strtolower($word);
	
	// only record keywords we're specifically scraping for
	if(array_key_exists($word, $this->allowedWords))
	  {
	    echo "Saw word: ".$word;
	    $rating = $this->rating($word, $body_words[0]);
	    
	    $q = 'SELECT id
				      FROM keywords
				      WHERE word = "' . $word          . '"';
	    //      AND time = "' . $this->timeStart . '"';
	    
	    $q = $this->database->query($q);
	    
	    // word has never been seen before in this session - create a new entry
	    if(mysql_num_rows($q) == 0)
	      {
		$q = 'INSERT INTO keywords (word, time, rating)
					      VALUES("' . $word                       . '",
					             "' . $this->timeStart            . '",
					             "' . $rating . '")';
	      }
	    // word has already been seen before in this session - increment its count
	    else
	      {
		$q = 'UPDATE keywords
					      SET count = count + 1,
					      rating = rating + ' . $rating  . '
					      WHERE word = "' . $word . '"
					      AND time = "' . $this->timeStart . '"';
	      }
	    
	    $this->database->query($q);
	  }
      }
  }

	
	// ========================================================================
	// insertPost
	//    args:  string - author's username
	//           string - time of in plain english
	//           string - message that the author wrote
	//           int - thread ID that this post belongs to
	//    ret:   void
	//    about: Inserts stats about a post in the database. If we've never 
	//           seen this author before, create an entry for him in the user
	//           table. If we've already seen him before, then update his
	//           post count.  
	//   fix:    There will be duplicate SQL error if a guy's username is over
	//           40 characters (the limit of our varchar for the name).
	// ------------------------------------------------------------------------	
	private function insertPost($author, $time, $message, $threadId)
	{
		// attempt to find existing record of this user
		$q = 'SELECT created, id 
			  FROM users
			  WHERE name = "' . $author . '"';
		
		$q = $this->database->query($q);
		
		// user does not exist - create new one 
		if(mysql_num_rows($q) == 0)
		{
			$u = 'INSERT INTO users (name, created)
				  VALUES("' . $author . '", 
						 "' . time()  . '")';
			
			$this->database->query($u);
			
			$userId = mysql_insert_id();
		}
		// user already exists - update existing record
		else
		{
			$c = mysql_fetch_array($q);
			
			if($c['created'] > time())
				$currentMinJoinTime = time();
			else
				$currentMinJoinTime = $c['created'];
		
			$u = 'UPDATE users
				  SET posts = posts + 1, 
				  created = "' . $currentMinJoinTime . '"
				  WHERE id = "' . $c['id'] . '"';
			
			$this->database->query($u);
			
			$userId = $c['id'];
		}
	
		$q = 'INSERT INTO posts (user, time, length, thread)
			  VALUES("' . $userId                 . '", 
					 "' . $this->cleanTime($time) . '", 
					 "' . strlen($message)        . '",
					 "' . $threadId               . '")';
		
		$this->database->query($q);	
		
		$this->insertMessage($message, $this->cleanTime($time));
	}

	
	// ========================================================================
	// insertMessage
	//    args:  string - message body text
	//           int - unix timestamp of the message
	//    ret:   void
	//    about: Inserts stats for the message body. 
	// ------------------------------------------------------------------------	
	private function insertMessage($body, $time)
	{
		$this->insertLinks($body);
		$this->insertKeywords($body, $time);
	}

	
	// ========================================================================
	// insertLinks
	//    args:  string - message body text
	//    ret:   void
	//    about: Finds all hyperlinks in the message body and updates their
	//           stats. All links with the same baselink (domain.suffix) are 
	//           stored as one. Doing so would give us more meaningful stats 
	//           because there are just billions of links in existence and the
	//           chances of any two matching up exactly is low.
	//    fix:   link regex does not work
	// ------------------------------------------------------------------------
	private function insertLinks($body)
	{
		// search for base links
		preg_match_all('#(http://)?(www\.)?(.*?\.[a-zA-Z]{3})#', $body, $links);
		
		foreach($links[2] as $link)
		{
			$q = 'SELECT id 
			      FROM links
			      WHERE baseUrl = "' . $link .'"';
			
			// this is a link we've never seen before - create a new entry
			if(mysql_num_rows($this->database->query($q)) == 0)
			{
				$q = 'INSERT INTO links (baseUrl)
				      VALUES("' . $link . '")';
			}
			// we already have a record of this link - increase it's count
			else
			{
				$q = 'UPDATE links
				      SET count = count + 1
				      WHERE baseUrl = "' . $link . '"';
			}
			
			$this->database->query($q);
		}
	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// INITIALIZATION FUNCTIONS ...................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	

	// ========================================================================
	// loadAllowedWords
	//    args:  none
	//    ret:   array of strings
	//    about: Returns an array of the keywords which we want to scrape for.
	// ------------------------------------------------------------------------	
	private function loadAllowedWords()
	{
		$q = 'SELECT ak.word 
		      FROM allowedkeywords AS ak, websites AS w
		      WHERE w.name = "' . $this->domain . '"
		      AND w.community = ak.community';
		
		$q = $this->database->query($q);

		while($row = mysql_fetch_array($q))
			$allowedWords[] = $row['word'];
		
		return $allowedWords;
	}
	
	// ========================================================================
	// loadGoodWords
	//    args:  none
	//    ret:   array of strings
	//    about: Returns a list of positive English words, which we've defined.
	//           Used for linguistic analysis.
	// ------------------------------------------------------------------------	
	private function loadGoodWords()
	{
		$q = 'SELECT word
		      FROM goodwords';
		
		$q = $this->database->query($q);
		
		while($row = mysql_fetch_array($q))
			$goodWords[] = $row['word'];
		
		return $goodWords;
	}
	
	// ========================================================================
	// loadBadWords
	//    args:  none
	//    ret:   array of strings
	//    about: Returns a list of negative English words, which we've defined.
	//           Used for linguistic analysis.
	// ------------------------------------------------------------------------	
	private function loadBadWords()
	{
		$q = 'SELECT word
		      FROM badwords';
		
		$q = $this->database->query($q);
		
		while($row = mysql_fetch_array($q))
			$badWords[] = $row['word'];
		
		return $badWords;
	}

	// ========================================================================
	// loadPlugin
	//    args:  none
	//    ret:   associative array of strings
	//    about: Returns the regular expressions necessary to scrape this 
	//           website.
	// ------------------------------------------------------------------------	
	private function loadPlugin()
	{
		$q = 'SELECT startPage, linkStructure, nextPageOfThreads, nextPageOfPosts, threadUrl, 
		      threadNumPosts, threadNumViews, threadTitle, firstPostAuthor, firstPostTime,
		      firstPostMessage, replyAuthor, replyTime, replyMessage 
		      FROM regexes
		      WHERE id = 0';
			
		return $this->database->fetch($q);
	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// HELPER FUNCTIONS ...........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	// ========================================================================
	// cleanTime
	//    args:  string - ie: Oct 08 '08 at 11:36am
	//    ret:   int - Unix timestamp
	//    about: Removes all symbols or words that could confuse PHP's 
	//           strtotime() and returns the Unix timestamp.
	//    fix:   Only works for threadless.com's time syntax right now.
	// ------------------------------------------------------------------------	
	private function cleanTime($englishTime)
	{
		$dirty[] = "'";
		$dirty[] = 'at';
		
		foreach($dirty as $dirt)
			$englishTime = str_replace($dirt, '', $englishTime);
		
		return strtotime($englishTime);
	}
	
	// ========================================================================
	// rating
	//    args:  * string - the word which you want to know the rating of
	//           * array of strings - the body of text that the word belongs to
	//    ret:   int - the magnitude of sign of this number tell how good (+)
	//                 or how bad (-) this word is spoken about.
	//    about: Removes all symbols or words that could confuse PHP's 
	//           strtotime() and returns the Unix timestamp.
	//    fix:   * "like" is not necessarily a positive word because it could 
	//             be used as a synonym of "similar" instead of "love".
	//           * do stemming and augmenting of goodWords.
	//           * bad running time
	//           * not scientificly proven algorithm
	//           * if $word belonged to goodWords or badWords, there is a 
	//             chance of division by zero.
	// ------------------------------------------------------------------------
	private function rating($word, $body)
	{
	  echo "Rating " . $word;
		$rating = 0;

		$scannedWord = "";
		$locationWord = 0;
		$locationAdjective = 0;
	
		// find location of the word in the body
		while($scannedWord != $word)
		{
			$scannedWord = $body[$locationWord];
			$locationWord++;
		}
		
		// scan through the whole body
		foreach($body as $adjective)
		{
			// don't care about capitalization
			$adjective = strtolower($adjective);
		
			// goodness of a word is inversely proportional to it's distance from a good word
			if(in_array($adjective, $this->goodWords))
				$rating += 1 / abs($locationAdjective - $locationWord);
			
			// badness of a word is inversely proportional to it's distance from a bad word
			if(in_array($adjective, $this->badWords))
				$rating -= 1 / abs($locationAdjective - $locationWord);
			
			$locationAdjective++;
		}
		
		return $rating;
	}
}
?>
