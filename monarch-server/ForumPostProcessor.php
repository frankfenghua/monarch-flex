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
require_once 'constants.php';

class ForumPostProcessor implements Processor {

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $database;     // connection to the master DB or specific community's DB
	private $domain;       // name of the community
	private $plugin;       // regular expressions for how to scrape this community
	private $timeStart;    // the time when we started scraping (used for time based statistics)
	private $allowedWords; // array of keywords which we are scraping for 
	private $linguistics;  // examines what and how the speaker is speaking

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// MAIN FUNCTIONS .............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	

	// FIX: where is the function header?
	public function ForumPostProcessor($domain) 
	{
		$this->domain       = $domain;
		$this->database     = new Database('master');
		$this->allowedWords = $this->loadAllowedWords();
		$this->database     = new Database($domain);
		$this->plugin       = $this->loadPlugin();
		$this->timeStart    = time();
		$this->linguistics  = new Linguistics();
	}

	// FIX: where is the function header?
	public function process($html, $thisUrl, $parentUrl) 
	{
		echo 'Parent url for '.$thisUrl.' is '.$parentUrl.'</br>';
		// Find the thread ID for this page using the link
		$threadUrl = $parentUrl;
		
		$q = 'SELECT id
			FROM threads
			WHERE url = "' . $threadUrl . '"';

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
		
		if($this->plugin['firstPostAuthorUrl'] != NULL)
			preg_match_all($this->plugin['firstPostAuthorUrl'], $html, $firstPostAuthorUrl);
		
		if($this->plugin['firstPostTime'] != NULL)
			preg_match_all($this->plugin['firstPostTime'], $html, $firstPostTime);
		
		if($this->plugin['firstPostMessage'] != NULL)
			preg_match_all($this->plugin['firstPostMessage'], $html, $firstPostMessage);
		
		if($this->plugin['replyAuthor'] != NULL)
			preg_match_all($this->plugin['replyAuthor'], $html, $replyAuthors);
		
		if($this->plugin['replyAuthorUrl'] != NULL)
			preg_match_all($this->plugin['replyAuthorUrl'], $html, $replyAuthorsUrls);	
		
		if($this->plugin['replyTime'] != NULL)
			preg_match_all($this->plugin['replyTime'], $html, $replyTimes);
		
		if($this->plugin['replyMessage'] != NULL)
			preg_match_all($this->plugin['replyMessage'], $html, $replyMessages);

		// insert the starting post
		$this->insertPost($firstPostAuthor[1][0], $firstPostAuthorUrl[1][0], $firstPostTime[1][0], $firstPostMessage[1][0], $threadId);
		
		// insert all the following replies
		for($i = 0; $i < sizeof($replyAuthors[1]); $i++)
			$this->insertPost($replyAuthors[1][$i], $replyAuthorsUrls[1][$i], $replyTimes[1][$i], $replyMessages[1][$i], $threadId);
	}
	
	// ========================================================================
	// insertPost
	//    args:  string - author's username
	//           string - link to the author's profile page
	//           string - time of in plain English
	//           string - message that the author wrote (can contain HTML)
	//           int - thread ID that this post belongs to
	//    ret:   void
	//    about: Updates info about the post author and analyze's his message.
	//           Will not do anything if we've already scraped this post before.
	// ------------------------------------------------------------------------	
	private function insertPost($author, $authorUrl, $time, $bodyHtml, $threadId)
	{
		$q = 'SELECT id 
			FROM users
			WHERE name = "' . $author . '"';
		
		$q = $this->database->query($q);

		// first time we've seen this author, so impossible to be a duplicate post
		if(mysql_num_rows($q) == 0)
		{
			$userId = -1;
		}
		// author has been seen previously, must check if this is a duplicate post
		else
		{
			$q = mysql_fetch_array($q);
			$userId = $q['id'];
			
			$q = 'SELECT id 
				FROM posts
				WHERE user = "' . $userId . '"
				AND time = "' . $this->englishToUnixTime($time) . '"
				AND hash = "' . substr(hash('md5', $bodyHtml), 0, POST_HASH_LENGTH) . '"';

			$q = $this->database->query($q);
		
			// do not re-scrape this post if we've already encountered it.
			if(mysql_num_rows($q) > 0)
			{
				if(DEBUG_POST)
					printf('<h4><font color="chocolate">found duplicate post written by %s on %s</font></h4>', $author, $time);
					
				return;
			}
			else
			{
				if(DEBUG_POST)
					printf('<h4><font color="chocolate">found new post written by %s on %s</font></h4>', $author, $time);
				
				$q = 'UPDATE threads
					SET posts = posts + 1
					WHERE id = ' . $threadId;
			
				$this->database->query($q);
			}
		}
		
		$actualUserId = $this->insertUser($author, $authorUrl, $userId, $time, $threadId, $bodyHtml);
		$this->insertKeywords($bodyHtml, $actualUserId, $threadId);
		$this->insertLinks($bodyHtml);
	}
	
	// ========================================================================
	// insertUser
	//    args:  string - author's username
	//           stinrg - link to the author's profile page
	//           int - if user exists, his ID from the database. If he does not
	//                 exist, -1.
	//           string - time of in plain english
	//           int - thread ID that this post belongs to
	//           string - message that the author wrote (can contain HTML)
	//    ret:   int - same as the argument if user already existed or a new ID
	//                 number if the user was new. 
	//    about: Inserts stats about a post in the database. If we've never 
	//           seen this author before, create an entry for him in the user
	//           table. If we've already seen him before, then update his
	//           post count.  
	//   FIX:    * There will be duplicate SQL error if a guy's username is over
	//             40 characters (the limit of our varchar for the name).
	// ------------------------------------------------------------------------	
	private function insertUser($author, $authorUrl, $userId, $time, $threadId, $bodyHtml)
	{	
		// user does not exist - create new one 
		if($userId == -1)
		{
			$q = 'INSERT INTO users (name, url, created)
				VALUES("' . mysql_real_escape_string($author) . '", 
				"' . mysql_real_escape_string($authorUrl) . '",
				"' . time() . '")';
			
			$this->database->query($q);
			
			$userId = mysql_insert_id();
		}
		// user already exists - update existing record
		else
		{
			$q = 'SELECT created 
			  FROM users
			  WHERE name = "' . mysql_real_escape_string($author) . '"';
			  
			$q = $this->database->fetch($q);
			
			// update his best known earliest post time
			if($q['created'] > time())
				$currentMinJoinTime = time();
			else
				$currentMinJoinTime = $q['created'];
		
			$q = 'UPDATE users
				SET posts = posts + 1, 
				created = "' . $currentMinJoinTime . '"
				WHERE id = "' . $userId . '"';
			
			$this->database->query($q);
		}
		
		// hash ensures that two consecutive posts within the same time granularity are counted as unique
		// given that this double posting idiot didn't post the same message twice. If he did, then its
		// OK because we don't want to count spam twice. 
		$q = 'INSERT INTO posts (user, time, length, thread, hash)
			VALUES("' . $userId . '", 
			"' . $this->englishToUnixTime($time) . '", 
			"' . strlen($bodyHtml) . '",
			"' . $threadId . '",
			"' . hash('md5', $bodyHtml) . '")';
			
		$this->database->query($q);	
		
		return $userId;
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
	// ------------------------------------------------------------------------
	private function insertLinks($bodyHtml)
	{
		// find all full URL's
		preg_match_all('#(?:href|src)="([^"]+)"#i', $bodyHtml, $fullUrls);
		
		$fullUrls = $fullUrls[1];
		
		// so the in_array will work below...
		for($i = 0; $i < sizeof($fullUrls); $i++)
			$fullUrls[$i] = strtolower($fullUrls[$i]);
		
		// remove all HTML but the english words and full URL's
		$bodyHtml   = preg_replace('#<a.*href[ ]*=[ ]*"#i', '', $bodyHtml);  // remove HTML before anchor URL
		$bodyHtml   = preg_replace('#<img.*src[ ]*=[ ]*"#i', '', $bodyHtml); // remove HTML before image URL
		$bodyHtml   = preg_replace('#"[^>]*>#', ' ', $bodyHtml);             // remove HTML after anchor/image URL
		$bodyHtml   = preg_replace('#[ ]+[\W]+[ ]+#', ' ', $bodyHtml);       // remove stray punctuation (artifact of the above)
		$bodyNoHtml = strip_tags($bodyHtml);                                 // remove HTML not related to anchor/image
		$bodyNoHtml = preg_replace('/[\s]+/', ' ', $bodyNoHtml);             // force only one space between each word
		$bodyNoHtml = trim($bodyNoHtml);                                     // remove white space from the ends

		// english proficiency does not vary per occurrence of keyword (it has body-wide scope)	
		$englishProficiency = $this->linguistics->englishProficiency($bodyHtml);
		
		$wordsArray = $this->linguistics->cleanBody($bodyNoHtml);
		
		for($linkLocation = 0; $linkLocation < sizeof($wordsArray); $linkLocation++)
		{
			if(in_array($wordsArray[$linkLocation], $fullUrls))
			{
				$baseUrl = $this->baseUrl($wordsArray[$linkLocation]);
				$goodness = $this->linguistics->goodnessByIndex($linkLocation, $wordsArray);
				$this->insertTimeStat('link', $baseUrl, $goodness, $englishProficiency);
				
				if(DEBUG_SAY_LINK)
					printf('<h4>base url seen: %s with goodness: %f and english proficiency: %f</h4>', 
						$baseUrl, $goodness, $englishProficiency);
			}
		}
	}
	
	// ========================================================================
	// insertKeywords
	//    args:  string - message body text
	//           int - ID of the user who said this message
	//           int - ID of the thread this post belongs to
	//    ret:   void
	//    about: Inserts stats each word found in the message body. 
	//    FIX:   * Why are spaces still being counted as a word?
	//           * Need to only store keywords that were said between this 
	//             session and the last session.
	// ------------------------------------------------------------------------	
	public function insertKeywords($bodyHtml, $userId, $threadId) 
	{
		// english proficiency does not vary per occurrence of keyword (it has body-wide scope)	
		$englishProficiency = $this->linguistics->englishProficiency($bodyHtml);
		
		$wordsArray = $this->linguistics->cleanBody($bodyHtml);
		
		for($wordLocation = 0; $wordLocation < sizeof($wordsArray); $wordLocation++)
		{
			if(in_array($wordsArray[$wordLocation], $this->allowedWords))
			{
				$goodness = $this->linguistics->goodnessByIndex($wordLocation, $wordsArray);
				$keywordId = $this->insertTimeStat('keyword', $wordsArray[$wordLocation], $goodness, $englishProficiency);
				
				// note that only keywords has the following two lines
				// right now we don't care what people / threads are saying about links (should we?)
				$this->insertUniStat('user', $userId, $keywordId, $goodness, $englishProficiency);
				$this->insertUniStat('thread', $threadId, $keywordId, $goodness, $englishProficiency);
				
				if(DEBUG_SAY_KEYWORD)
					printf('<h4>keyword "%s" said by userId #%d with goodness: %f and english proficiency: %f</h4>', 
						$wordsArray[$wordLocation], $userId, $goodness, $englishProficiency);
			}
		}
	}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// HELPER FUNCTIONS ...........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	// ========================================================================
	// englishToUnixTime
	//    args:  string - ie: Oct 08 '08 at 11:36am
	//    ret:   int - Unix timestamp
	//    about: Removes all symbols or words that could confuse PHP's 
	//           strtotime() and returns the Unix timestamp.
	//    FIX:   Only tested for threadless.com's time syntax right now.
	// ------------------------------------------------------------------------	
	private function englishToUnixTime($englishTime)
	{
		$dirty[] = "'";
		$dirty[] = 'at';
		$dirty[] = '&nbsp;'; // TODO: html_entity_decode is supposed to be used to remove ALL entities, but it fucks up shit.
		
		foreach($dirty as $dirt)
			$englishTime = str_replace($dirt, ' ', $englishTime);

		$englishTime = strip_tags($englishTime);
		
		return strtotime($englishTime);
	}
	
	// ========================================================================
	// insertUniStat
	//    args:  string - 'thread' or 'user'
	//           int - ID of the thread which this keyword was seen in
	//           int - ID of the keyword
	//           float - goodness rating of the thing
	//           float - english proficiency rating of the thing
	//    ret:   void
	//    about: This function should be only used for thread or user stats, 
	//           which are not time based. They are just running sums of values
	//           over the time starting from when the community was created. 
	//           Creates a stat with the specified values and a link from the 
	//           thing to the stat. Adds on the values if the stat already 
	//           exists. We only keep track of how threads and users are 
	//           talking about keywords, not links.
	// ------------------------------------------------------------------------	
	private function insertUniStat($type, $thingId, $keywordId, $goodness, $englishProficiency)
	{	
		if($type != 'thread' && $type != 'user')
			die('ForumPostProcessor->insertUniStat(): unrecognized argument for $type');
	
		$q = 'SELECT stat
			FROM ' . $type . 'stats
			WHERE ' . $type . ' = ' . $thingId . '
			AND keyword = ' . $keywordId;
		
		$q = $this->database->query($q);
		
		// thing does not have any existing stats
		if(mysql_num_rows($q) == 0)
		{
			$q = 'INSERT INTO stats (time, count, goodness, englishProficiency)
				VALUES("' . $this->timeStart . '",
				"1",
				"' . $goodness . '", 
				"' . $englishProficiency . '")';
			
			$this->database->query($q);
			
			$statId = mysql_insert_id();
			
			$q = 'INSERT INTO ' . $type . 'stats (' . $type . ', keyword, stat)
				VALUES("' . $thingId . '",
				"' . $keywordId . '", 
				"' . $statId . '")';
			
			$this->database->query($q);
		}
		// thing has existing stat record
		else
		{
			$q = mysql_fetch_array($q);
			
			$statId = $q['stat'];
			
			$q = 'UPDATE stats
				SET time = ' . $this->timeStart . ',
				count = count + 1,
				goodness = goodness + ' . $goodness . ',
				englishProficiency = englishProficiency + ' . $englishProficiency . '
				WHERE id = ' . $statId;
				
			$this->database->query($q);	
		}
	}
	
	// ========================================================================
	// insertTimeStat
	//    args:  string - 'link' or 'keyword'
	//           string - the baseUrl or keyword that was just found.
	//           float - goodness rating of the thing
	//           float - english proficiency rating of the thing
	//    ret:   the id of the thing that was found
	//    about: Should only be used by links and keywords, which have changing
	//           stats over time. Updates the stat matching this crawl session
	//           time (or creates one if it doesn't exist), then links the 
	//           thing to this stat (if it doesn't exist).
	// ------------------------------------------------------------------------	
	private function insertTimeStat($type, $thing, $goodness, $englishProficiency)
	{	
		switch($type)
		{
			case 'link':    $typeColumn = 'baseUrl'; break;
			case 'keyword': $typeColumn = 'word';    break;
			default:        die('ForumPostProcessor->insertTimeStat: unrecognized argument for $type');
		}
			
		$q = 'SELECT id FROM ' . $type . 's
			WHERE ' . $typeColumn . ' = "' . mysql_real_escape_string($thing) . '"';
		
		$q = $this->database->query($q);
		
		// thing has never been seen before in previous sessions
		if(mysql_num_rows($q) == 0)
		{
			$q = 'INSERT INTO ' . $type . 's (' . $typeColumn . ')
				VALUES("' . mysql_real_escape_string($thing) . '")';
			
			$q = $this->database->query($q);
			
			$thingId = mysql_insert_id();
		}
		// thing has been seen before in previous sessions
		else
		{
			$q = mysql_fetch_array($q);
		
			$thingId = $q['id'];
		}
		
		$q = 'SELECT s.id
			FROM ' . $type . 'stats AS xs, stats AS s
			WHERE xs.' . $type . ' = "' . $thingId . '"
			AND xs.stat = s.id
			AND s.time = "' . $this->timeStart . '"';
			
		$q = $this->database->query($q);
		
		// thing has not been seen in this particular session
		if(mysql_num_rows($q) == 0)
		{
			$q = 'INSERT INTO stats (time)
				VALUES("' . $this->timeStart . '")';

			$q = $this->database->query($q);

			$statId = mysql_insert_id();
			
			$q = 'INSERT INTO ' . $type . 'stats (' . $type . ', stat)
				VALUES("' . $thingId  . '",
				"' . $statId . '")';
				
			$q = $this->database->query($q);
		}
		// thing has been seen in this particular session
		else
		{
			$q = mysql_fetch_array($q);
				
			$statId = $q['id'];
		}
		
		// FIX: englishProficiency needs to be / by count on the GUI, since this is a running sum
		$q = 'UPDATE stats
			SET count = count + 1,
			goodness = goodness + ' . $goodness  . ', 
			englishProficiency = englishProficiency + ' . $englishProficiency . '
			WHERE id = "' . $statId . '"';
	
		$this->database->query($q);
		
		return $thingId;
	}
	
	// ========================================================================
	// baseUrl
	//    args:  string - a complete URL
	//    ret:   string - the base URL
	//    about: Turns something like "http://www.yahoo.com/folder/page.html"
	//           into "yahoo.com". We only store base URL's in our database
	//           because it allows for richer stats. 
	//    FIX:   don't do mysql_real_escape_string here. It was put in just to
	//           avoid errors. Escaping should be done when you're doing the 
	//           actual querying. 
	// ------------------------------------------------------------------------	
	private function baseUrl($fullUrl)
	{
		$cleanLink = str_ireplace('http://', '', $fullUrl);
		$cleanLink = str_ireplace('www.', '', $cleanLink);

		preg_match_all(sprintf('#(?:[a-z]+\.)?([a-z0-9\-]+\.(?:%s)(\.(?:%s))?)#i', 
			REGEX_DOMAIN_SUFFIXES, REGEX_DOMAIN_SUFFIXES), $cleanLink, $baseUrl);

		$baseUrl = $baseUrl[1][0];
		return mysql_real_escape_string(strtolower($baseUrl));
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
		$q = 'SELECT *
		      FROM regexes';
			
		return $this->database->fetch($q);
	}

}
?>
