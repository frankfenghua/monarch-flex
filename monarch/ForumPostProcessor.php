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
require_once 'Linguistics.php';
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
	// private $badWords;     // array of negative words (linguistic analysis)
	// private $goodWords;    // array of positive words (linguistic analysis)
	private $start_time;  // Time this processor was created
	private $linguistics;  // calculates stats for 

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// MAIN FUNCTIONS .............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	

	// FIX: where is the function header?
	public function ForumPostProcessor($domain) 
	{
		$this->domain       = $domain;
		$this->database     = new Database('master');
		$this->allowedWords = $this->loadAllowedWords();
		// $this->badWords     = $this->loadBadWords();
		// $this->goodWords    = $this->loadGoodWords();
		$this->database     = new Database($domain);
		$this->plugin       = $this->loadPlugin();
		$this->timeStart    = time();
		$this->linguistics  = new Linguistics();
	}

	// FIX: where is the function header?
	public function process($html) 
	{
		// $this->dummy($html);
		
		// Find the thread ID for this page using the link
		$threadUrl = $this->plugin['parentUrl'];
		preg_match_all($threadUrl, $html, $matches);
		// print_r($matches);
		$q = 'SELECT id
			FROM threads
			WHERE url = "' . $matches[1][0] . '"';
		$q = $this->database->fetch($q);
		$threadID = $q[0] ? $q[0] : -1;
		$this->scrapePosts($threadID, $html);
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
		// find all full URL's
		preg_match_all('#(?:href|src)="([^"]+)"#i', $body, $fullUrls);
		
		// remove all HTML but the full URL's
		$body = preg_replace('#<a.*href[ ]*=[ ]*"#i', '', $body);
		$body = preg_replace('#<img.*src[ ]*=[ ]*"#i', '', $body);
		$body = preg_replace('#"[^>]*>#', ' ', $body);
		$body = strip_tags($body);
		$body = preg_replace('/[\s]+/', ' ', $body);
		$body = trim($body);
		
		/*
		echo '<h1>$body before explode</h1>' . $body . '<br>';
		
		$body = explode(' ', $body);
		
		echo '<h1>$body after explode</h1>' . $body . '<br>';
		*/

		foreach($fullUrls[1] as $fullUrl)
		{
			// $positionsLinkInBody = array_keys($body, $fullUrl);
			
			// get base url
			$cleanLink = str_ireplace('http://', '', $fullUrl);
			$cleanLink = str_ireplace('www.', '', $cleanLink);
			
			$suffixes = 'com|net|info|org|me|tv|mobi|biz|us|ca|asia|ws|ag|am|at|be|cc|cn|de|eu|fm|fm|gs|jobs|jp|ms|nu|co|nz|tc|tw|idv|uk|vg';

			preg_match_all(sprintf('#(?:[a-z]+\.)?([a-z0-9\-]+\.(?:%s)(\.(?:%s))?)#i', $suffixes, $suffixes), $cleanLink, $baseUrl);
	
			$baseUrl = $baseUrl[1][0];

			$baseUrl = mysql_real_escape_string($baseUrl);

			// foreach($positionsLinkInBody as $positionLinkInBody) 
			//	$goodness += $this->linguistics->goodnessFromIndex($positionLinkInBody, $body);

			echo '<h1>$fullUrl</h1>' . $fullUrl . '<br>';
			echo '<h1>$body</h1>' . $body . '<br>';

			$goodness = $this->linguistics->goodness($fullUrl, $body);

			// $englishProficiency = $this->linguistics->englishProficiency(implode(' ', $body));
			$englishProficiency = $this->linguistics->englishProficiency($body);
			
			$q = 'SELECT id 
				FROM links 
				WHERE baseUrl = "' . $baseUrl  . '"';
			
			$q = $this->database->query($q);
			
			// link has never been seen in previous sessions
			if(mysql_num_rows($q) == 0)
			{
				$q = 'INSERT INTO links (baseUrl)
					VALUES("' . $baseUrl . '")';
				
				$q = $this->database->query($q);
				
				$linkId = mysql_insert_id();
			}
			// link has been seen in previous sessions
			else
			{
				$q = mysql_fetch_array($q);
				
				$linkId = $q['id'];
			}
			
			$q = 'SELECT s.id
				FROM linkstats AS ls, stats AS s
				WHERE ls.link = "' . $linkId . '"
				AND ls.stat = s.id
				AND s.time = "' . $this->timeStart . '"';
			
			$q = $this->database->query($q);
			
			// link has not been seen in this particular session
			if(mysql_num_rows($q) == 0)
			{
				$q = 'INSERT INTO stats (time)
					VALUES("' . $this->timeStart . '")';

				$q = $this->database->query($q);

				$statId = mysql_insert_id();
				
				$q = 'INSERT INTO linkstats (link, stat)
					VALUES("' . $linkId  . '",
					"' . $statId . '")';
					
				$q = $this->database->query($q);
			}
			// link has been seen in this particular session
			else
			{
				$q = mysql_fetch_array($q);
					
				$statId = $q['id'];
			}
			
			// FIX: englishProficiency needs to be / by count on the GUI.
			$q = 'UPDATE stats
				SET count = count + 1,
				goodness = goodness + ' . $goodness  . ', 
				englishProficiency = englishProficiency + ' . $englishProficiency . '
				WHERE id = "' . $statId . '"';
		
			$this->database->query($q);		
		}
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
	public function insertKeywords($html) 
	{
		// remove whitespace and HTML
		/*
		$body = preg_replace('/[\s]+/', ' ', $html);
		$body = preg_replace('/[!-~]+/', ' ', $html);
		*/
		$body = strip_tags($html);
		
		preg_match_all('/[a-z]+/i', $body, $words);
		
		foreach($words[0] as $word)
		{	
			// don't care about capitalization  
			$word = strtolower($word);
			
			// only record keywords we're specifically scraping for
			if(in_array($word, $this->allowedWords))
			{
				$goodness = $this->linguistics->goodness($word, $body);
				$englishProficiency = $this->linguistics->englishProficiency($body);
				
				$q = 'SELECT id FROM keywords
					WHERE word = "' . $word . '"';
				
				$q = $this->database->query($q);
				
				// word has never been seen before in previous sessions
				if(mysql_num_rows($q) == 0)
				{
					$q = 'INSERT INTO keywords (word)
						VALUES("' . $word . '")';
					
					$q = $this->database->query($q);
					
					$keywordId = mysql_insert_id();
				}
				// word has been seen before in previous sessions
				else
				{
					$q = mysql_fetch_array($q);
				
					$keywordId = $q['id'];
				}
				
				$q = 'SELECT s.id
					FROM keywordstats AS ks, stats AS s
					WHERE ks.keyword = "' . $keywordId . '"
					AND ks.stat = s.id
					AND s.time = "' . $this->timeStart . '"';
					
				$q = $this->database->query($q);
				
				// keyword has not been seen in this particular session
				if(mysql_num_rows($q) == 0)
				{
					$q = 'INSERT INTO stats (time)
						VALUES("' . $this->timeStart . '")';
				
					// printf('<h2>%s</h2>', $q);

					$q = $this->database->query($q);

					$statId = mysql_insert_id();
					
					$q = 'INSERT INTO keywordstats (keyword, stat)
						VALUES("' . $keywordId  . '",
						"' . $statId . '")';
						
					$q = $this->database->query($q);
				}
				// keyword has been seen in this particular session
				else
				{
					$q = mysql_fetch_array($q);
						
					$statId = $q['id'];
				}
				
				// FIX: englishProficiency needs to be / by count on the GUI.
				$q = 'UPDATE stats
					SET count = count + 1,
					goodness = goodness + ' . $goodness  . ', 
					englishProficiency = englishProficiency + ' . $englishProficiency . '
					WHERE id = "' . $statId . '"';
			
				$this->database->query($q);
			}
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
			$allowedWords[] = strtolower($row['word']);
		
		return $allowedWords;
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
		      firstPostMessage, replyAuthor, replyTime, replyMessage, parentUrl
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
	
}
?>
