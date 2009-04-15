<?php

// TITLE:   DetailStats
// TYPE:    class with automatic instantiation
// AUTHOR:  Ryan Lin, Andrew Spencer
// CREATED: April 14, 2009
// ABOUT:   prints out an XML file showing detailed stats of a website
// USAGE:   DetailStats.php?websiteName=1 or 
//          DetailStats.php?websiteName=threadless
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

header('Content-Type: text/xml');

require_once('database/Database.php');
require_once('../constants.php');

class DetailStats
{

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// FIELDS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	private $database; // connection to a specific website's database

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
		
		echo '<detailStats>';
		
		echo '<general>';
		$this->general();
		echo '</general>';
		
		echo '<keywords>';
		$this->keywords();
		echo '</keywords>';
		
		echo '</detailStats>';
	}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// PRIVATE METHODS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// ========================================================================
	// keywords
	//    args:  none
	//    ret:   void
	//    about: Prints out general stats about the website
	// ------------------------------------------------------------------------
	private function general()
	{
		// total number of users
		$q = 'SELECT COUNT(*) FROM users';
		$q = $this->database->fetch($q);
		echo '<numberUsers>' . $q[0] . '</numberUsers>';
		
		// posts per day
		$q = 'SELECT ((MAX(time) - MIN(time)) / ' . SECONDS_IN_DAY . ') FROM posts';
		$q = $this->database->fetch($q);
		echo '<postsPerDay>' . $q[0] . '</postsPerDay>';
		
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
		
		$q = 'SELECT url, name, posts AS rating 
			FROM users 
			ORDER BY rating DESC
			LIMIT 3';
		$this->usersGroup('parrots', $q);
		
		$q = 'SELECT url, name, created AS rating 
			FROM users 
			ORDER BY rating DESC
			LIMIT 3';
		$this->usersGroup('newbies', $q);
		
		$q = 'SELECT url, name, created AS rating 
			FROM users 
			ORDER BY rating ASC
			LIMIT 3';
		$this->usersGroup('veterans', $q);
		
		$q = 'SELECT title, url, posts AS rating 
			FROM threads 
			ORDER BY rating DESC
			LIMIT 3';
		$this->threadsGroup('livelyThreads', $q);	
	}

	// ========================================================================
	// keywords
	//    args:  none
	//    ret:   void
	//    about: Prints out various groupings of threads for each keyword
	// ------------------------------------------------------------------------
	private function keywords()
	{
		$q = 'SELECT id, word FROM keywords';
		$keywordsQuery = $this->database->query($q);
		
		while($keywordRow = mysql_fetch_array($keywordsQuery))
		{
			echo '<keyword word="' . $keywordRow['word'] . '">';
		
			$q = 'SELECT t.url, t.title, s.goodness AS rating
				FROM threads AS t, threadstats AS ts, stats AS s
				WHERE t.id = ts.thread
				AND ts.keyword = ' . $keywordRow['id'] . '
				AND ts.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->threadsGroup('loveThreads', $q);
			
			$q = 'SELECT t.url, t.title, s.goodness AS rating
				FROM threads AS t, threadstats AS ts, stats AS s
				WHERE t.id = ts.thread
				AND ts.keyword = ' . $keywordRow['id'] . '
				AND ts.stat = s.id
				ORDER BY rating ASC
				LIMIT 3';
			
			$this->threadsGroup('hateThreads', $q);
			
			$q = 'SELECT t.url, t.title, s.count AS rating
				FROM threads AS t, threadstats AS ts, stats AS s
				WHERE t.id = ts.thread
				AND ts.keyword = ' . $keywordRow['id'] . '
				AND ts.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->threadsGroup('hotThreads', $q);
			
			$q = 'SELECT u.name, u.url, s.goodness AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->usersGroup('assKissers', $q);
			
			$q = 'SELECT u.name, u.url, s.goodness AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				ORDER BY rating ASC
				LIMIT 3';
			
			$this->usersGroup('trashTalkers', $q);
			
			$q = 'SELECT u.name, u.url, s.count AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->usersGroup('parrots', $q);
			
			$q = 'SELECT u.name, u.url, s.englishProficiency AS rating
				FROM users AS u, userstats AS us, stats AS s
				WHERE u.id = us.user
				AND us.keyword = ' . $keywordRow['id'] . '
				AND us.stat = s.id
				ORDER BY rating DESC
				LIMIT 3';
			
			$this->usersGroup('sophisticatedOrators', $q);
			
			echo '</keyword>';
		}
	}

	// ========================================================================
	// threadsGroup
	//    args:  string - what type of threads are these? Hate threads? Love 
	//                    threads? etc...
	//           string - MySQL query for all threads of the aforementioned 
	//                    type
	//    ret:   void
	//    about: Prints out a group of threads.
	// ------------------------------------------------------------------------
	private function threadsGroup($type, $query)
	{
		echo '<' . $type . '>';
		
		$threadsQuery = $this->database->query($query);
		
		while($threadRow = mysql_fetch_array($threadsQuery))
			$this->threadNode($threadRow);
		
		echo '</' . $type . '>';
	}
	
	// ========================================================================
	// threadNode
	//    args:  array with the following keys:
	//              * url
	//              * title
	//              * rating
	//    ret:   void
	//    about: Prints out a thread node with data about this thread.
	// ------------------------------------------------------------------------
	private function threadNode($threadData)
	{
		echo '<thread>';
		echo '<url>' . $this->xml($threadData['url']) . '</url>';
		echo '<title>' . $this->xml($threadData['title']) . '</title>';
		echo '<rating>' . $this->xml($threadData['rating']) . '</rating>';
		echo '</thread>';
	}
	
	// ========================================================================
	// usersGroup
	//    args:  string - what type of users are these? Haters? Lovers? etc...
	//           string - MySQL query for all users of the aforementioned 
	//                    type
	//    ret:   void
	//    about: Prints out a group of users.
	// ------------------------------------------------------------------------
	private function usersGroup($type, $query)
	{
		echo '<' . $type . '>';
		
		$usersQuery = $this->database->query($query);
		
		while($userRow = mysql_fetch_array($usersQuery))
			$this->userNode($userRow);
		
		echo '</' . $type . '>';
	}
	
	// ========================================================================
	// userNode
	//    args:  array with the following keys:
	//              * url
	//              * name
	//              * rating
	//    ret:   void
	//    about: Prints out a user node with data about this user.
	// ------------------------------------------------------------------------
	private function userNode($userData)
	{
		echo '<user>';
		echo '<url>' . $this->xml($userData['url']) . '</url>';
		echo '<title>' . $this->xml($userData['name']) . '</title>';
		echo '<rating>' . $this->xml($userData['rating']) . '</rating>';
		echo '</user>';
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
		return $newData;
	}

}

$detailStats = new DetailStats($_GET['websiteName']);

?>
