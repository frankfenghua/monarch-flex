<?php

// TITLE:   DetailStats
// TYPE:    Class with automatic instantiation
// AUTHOR:  Ryan Lin, Andrew Spencer
// CREATED: April 14, 2009
// ABOUT:   prints out an XML file showing detailed stats of a website
// USAGE:   detailStats.php?websiteName=1 or 
//          detailStats.php?websiteName=threadless
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

header('Content-Type: text/xml');

require_once('../database/Database.php');
require_once('../constants.php');
require_once('../Url.php');

class DetailStats
{

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// FIELDS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	private $database; // connection to a specific website's database
	private $startPage;  // the start page for this website

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// PUBLIC METHODS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// ========================================================================
	// DetailStats
	//    args:  int [website ID] or string [name of website]
	//    ret:   none
	//    about: prints out an XML file showing detailed stats of a website
	// ------------------------------------------------------------------------
	public function DetailStats($website)
	{
		$this->database = new Database($website);

		$arr = $this->database->fetch('SELECT startPage FROM regexes');
		$this->startPage = $arr['startPage'];
		
		echo '<detailStats>';
		
		echo '<general>';
		$this->general();
		echo '</general>';
		
		echo '<links>';
		$this->links();
		echo '</links>';
		
		echo '<keywords>';
		$this->keywords();
		echo '</keywords>';
		
		echo '</detailStats>';
	}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// PRIVATE METHODS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// ========================================================================
	// general
	//    args:  none
	//    ret:   void
	//    about: Prints out general stats about the website
	// ------------------------------------------------------------------------
	private function general()
	{
		// website URL
		$q = 'SELECT startPage FROM regexes';
		$q = $this->database->fetch($q);
		echo '<url>' . $q[0] . '</url>';
		
		// total number of users
		$q = 'SELECT COUNT(*) FROM users';
		$q = $this->database->fetch($q);
		echo '<numberUsers>' . $q[0] . '</numberUsers>';
		
		// posts per day
		$q = 'SELECT ((MAX(time) - MIN(time)) / ' . SECONDS_IN_DAY . ') FROM posts';
		$q = $this->database->fetch($q);
		echo '<postsPerDay>' . $q[0] . '</postsPerDay>';
		
		// number of posts in the last 24 hours
		$q = 'SELECT COUNT(*) 
			FROM posts
			WHERE time > ' . (time() - SECONDS_IN_DAY);
		$q = $this->database->fetch($q);
		echo '<postsToday>' . $q[0] . '</postsToday>';
		
		// number of posts we have scraped
		$q = 'SELECT COUNT(*) FROM posts';
		$q = $this->database->fetch($q);
		echo '<analyzedPosts>' . $q[0] . '</analyzedPosts>';
		
		// number of threads we have scraped
		$q = 'SELECT COUNT(*) FROM threads';
		$q = $this->database->fetch($q);
		echo '<analyzedThreads>' . $q[0] . '</analyzedThreads>';
		
		// people with the most posts
		$q = 'SELECT url, name, posts AS rating 
			FROM users 
			ORDER BY rating DESC
			LIMIT 1';
		$this->usersGroup('chatterboxes', $q);
		
		// recently joined people
		$q = 'SELECT url, name, created AS rating 
			FROM users 
			ORDER BY rating DESC
			LIMIT 1';
		$this->usersGroup('newbies', $q);
		
		// people that joined the site a long time ago (that the crawler knows of)
		$q = 'SELECT url, name, created AS rating 
			FROM users 
			ORDER BY rating ASC
			LIMIT 1';
		$this->usersGroup('veterans', $q);
		
		// threads with the most posts
		$q = 'SELECT title, url, posts AS rating 
			FROM threads 
			ORDER BY rating DESC
			LIMIT 1';
		$this->threadsGroup('livelyThreads', $q);	
		
		// threads with the most posts
		$q = 'SELECT title, url, views AS rating 
			FROM threads 
			WHERE views > 0
			ORDER BY rating DESC
			LIMIT 1';
		$this->threadsGroup('mostViewedThreads', $q);	
	}
	
	//=========================================================================
	// links
	//    args:  none
	//    ret:   void
	//    about: stats about the external links people said
	//-------------------------------------------------------------------------
	private function links()
	{
		$q = 'SELECT id FROM links';
		$allLinks = $this->database->query($q);
		
		$mostMentionedLink  = $mostLikedLink   = $leastLikedLink   = '404error.html';
		$mostMentionedCount = $mostLikedRating = $leastLikedRating = 0;
		
		// go through the aggregate stats of each link and as we're going along, keep track of the min/max
		while($link = mysql_fetch_array($allLinks))
		{
			$q = 'SELECT l.id, l.baseUrl, SUM(s.count), SUM(s.goodness)
				FROM links AS l, linkstats AS ls, stats AS s
				WHERE l.id = ' . $link['id'] . '
				AND l.id = ls.link
				AND ls.stat = s.id
				GROUP BY l.id';
			
			$linkStats = $this->database->fetch($q);
			
			// the very first link has no previous link to compare to, so just set it as the most/least everything
			if($linkStats['SUM(s.count)'] > $mostMentionedCount || $linkStats['id'] == 1)
			{
				$mostMentionedCount = $linkStats['SUM(s.count)'];
				$mostMentionedLink  = $linkStats['baseUrl'];
			}
			
			if($linkStats['SUM(s.goodness)'] > $mostLikedRating || $linkStats['id'] == 1)
			{
				$mostLikedRating = $linkStats['SUM(s.goodness)'];
				$mostLikedLink   = $linkStats['baseUrl'];
			}
			
			if($linkStats['SUM(s.goodness)'] < $leastLikedRating || $linkStats['id'] == 1)
			{
				$leastLikedRating = $linkStats['SUM(s.goodness)'];
				$leastLikedLink   = $linkStats['baseUrl'];
			}
		}
		
		printf('<mostLiked rating="%s" url="%s" />', $mostLikedRating, $mostLikedLink);
		printf('<leastLiked rating="%s" url="%s" />', $leastLikedRating, $leastLikedLink);
		printf('<mostMentioned rating="%s" url="%s" />', $mostMentionedCount, $mostMentionedLink);
	}

	// ========================================================================
	// keywords
	//    args:  none
	//    ret:   void
	//    about: Prints out various groupings of threads for each keyword
	// ------------------------------------------------------------------------
	private function keywords()
	{
		$q = 'SELECT id, word FROM keywords ORDER BY word ASC';
		$keywordsQuery = $this->database->query($q);
		
		while($keywordRow = mysql_fetch_array($keywordsQuery))
		{
			echo '<keyword label="' . $keywordRow['word'] . '">';
		
			// threads that overall talk positively about the keyword
			$q = 'SELECT t.url, t.title, s.goodness AS rating
				FROM threads AS t, threadstats AS ts, stats AS s
				WHERE t.id = ts.thread
				AND ts.keyword = ' . $keywordRow['id'] . '
				AND ts.stat = s.id
				AND s.goodness > 0
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->threadsGroup('loveThreads', $q, true);
			
			// threads that overall talk negatively about the keyword
			$q = 'SELECT t.url, t.title, s.goodness AS rating
				FROM threads AS t, threadstats AS ts, stats AS s
				WHERE t.id = ts.thread
				AND ts.keyword = ' . $keywordRow['id'] . '
				AND ts.stat = s.id
				AND s.goodness < 0
				ORDER BY rating ASC
				LIMIT 3';
			
			$this->threadsGroup('hateThreads', $q, true);
			
			// threads that mention the keyword a lot
			$q = 'SELECT t.url, t.title, s.count AS rating
				FROM threads AS t, threadstats AS ts, stats AS s
				WHERE t.id = ts.thread
				AND ts.keyword = ' . $keywordRow['id'] . '
				AND ts.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->threadsGroup('hotThreads', $q, true);
			
			// people who talk most positively about the keyword
			$q = 'SELECT u.name, u.url, s.goodness AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				AND s.goodness > 0
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->usersGroup('assKissers', $q, true);
			
			// people who talk most negatively about the keyword
			$q = 'SELECT u.name, u.url, s.goodness AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				AND s.goodness < 0
				ORDER BY rating ASC
				LIMIT 3';
			
			$this->usersGroup('trashTalkers', $q, true);
			
			// people who talk a lot about the keyword
			$q = 'SELECT u.name, u.url, s.count AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->usersGroup('chatterboxes', $q, true);
			
			// people who talk with good prose about the keyword
			$q = 'SELECT u.name, u.url, s.englishProficiency / s.count AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->usersGroup('sophisticatedOrators', $q, true);
			
			echo '</keyword>';
		}
	}

	// ========================================================================
	// threadsGroup
	//    args:  string - what type of threads are these? Hate threads? Love 
	//                    threads? etc...
	//           string - MySQL query for all threads of the aforementioned 
	//                    type
	//           bool   - add "mentions" or "rating" after the value
	//    ret:   void
	//    about: Prints out a group of threads.
	// ------------------------------------------------------------------------
	private function threadsGroup($type, $query, $appendUnits = false)
	{
		echo '<' . $type . ' label="' . $type . '">';
		
		$threadsQuery = $this->database->query($query);
		
		while($threadRow = mysql_fetch_array($threadsQuery))
			$this->threadNode($threadRow, $appendUnits);
		
		echo '</' . $type . '>';
	}
	
	// ========================================================================
	// threadNode
	//    args:  array with the following keys:
	//              * url
	//              * title
	//              * rating
	//           bool   - add "mentions" or "rating" after the value
	//    ret:   void
	//    about: Prints out a thread node with data about this thread.
	// ------------------------------------------------------------------------
	private function threadNode($threadData, $appendUnits = false)
	{
		$url = URL::translateURLBasedOnCurrent($this->xml($threadData['url']),$this->startPage);
		$title = $this->xml($threadData['title']);
		$rating = $this->xml($threadData['rating']);
		
		if($appendUnits)
			$rating = $this->appendUnits($rating);
		
		echo '<thread url="' . $url . '" label="' . $title . '" rating="' . $rating . '"/>';
	}
	
	// ========================================================================
	// usersGroup
	//    args:  string - what type of users are these? Haters? Lovers? etc...
	//           string - MySQL query for all users of the aforementioned 
	//                    type
	//           bool   - add "mentions" or "rating" after the value
	//    ret:   void
	//    about: Prints out a group of users.
	// ------------------------------------------------------------------------
	private function usersGroup($type, $query, $appendUnits = false)
	{
		echo '<' . $type . ' label ="' . $type . '">';
		
		$usersQuery = $this->database->query($query);
		
		while($userRow = mysql_fetch_array($usersQuery))
			$this->userNode($userRow, $appendUnits);
		
		echo '</' . $type . '>';
	}
	
	// ========================================================================
	// userNode
	//    args:  array with the following keys:
	//              * url
	//              * name
	//              * rating
	//           bool   - add "mentions" or "rating" after the value
	//    ret:   void
	//    about: Prints out a user node with data about this user.
	// ------------------------------------------------------------------------
	private function userNode($userData, $appendUnits = false)
	{
		$url = URL::translateURLBasedOnCurrent($this->xml($userData['url']),$this->startPage);
		$title = $this->xml($userData['name']);
		$rating = $this->xml($userData['rating']);
		
		if($appendUnits)
			$rating = $this->appendUnits($rating);
		
		echo '<user url="' . $url . '" label="' . $title . '" rating="' . $rating . '"/>';
	}
	
	// ========================================================================
	// appendUnits
	//    args:  either a float or int
	//    ret:   string
	//    about: Based on whether this is an int or floay, we can determine if
	//           this is a count or some rating. We will truncate the float, so
	//           it's not too long.  We will append what kind of number this 
	//           is.
	// ------------------------------------------------------------------------
	private function appendUnits($number)
	{
		// int => count
		if(round($number) == $number)
			return $number . ' mentions';
		// float => sentiment | english proficiency
		else
			return sprintf('%.2f', $number) . ' rating';
	}

	// ========================================================================
	// xml
	//    args:  XML node data - string
	//    ret:   "Cleaned" parameter data
	//    about: Escapes all characters in the parameter string that might cause
	//        an XML parser to barf.
	// ------------------------------------------------------------------------
	private function xml($data) {
		$newData = str_replace('&', '', $data);
		$newData = str_replace('<', '\<', $newData);
		$newData = str_replace('"', '', $newData);
		return $newData;
	}

}

// instantiation of class
if(isset($_GET['websiteName']))
	$detailStats = new DetailStats($_GET['websiteName']);
else
	die('USAGE: detailStats.php?websiteName=1 or detailStats.php?websiteName=threadless');

?>
