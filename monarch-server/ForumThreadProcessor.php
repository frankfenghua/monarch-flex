<?php
  ////////////////////////////////////////////////////////////////////
  // ForumThreadProcessor.php                                            
  // Adapted by Andrew Spencer from code written by Ryan Lin
  //  November 2008
  //  
  // Defines the class ForumThreadProcessor which implements Processor;
  // 
  // 
  ////////////////////////////////////////////////////////////////////

require_once 'database/Database.php';
require_once 'Processor.php';
require_once 'Url.php';

class ForumThreadProcessor implements Processor {

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $database; // connection to the master DB or specific community's DB
	private $domain;   // name of the community
	private $plugin;   // regular expressions for how to scrape this community
	private $myUrl;    // the URL of the page that we are currently processing (String)
	
  
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// MAIN FUNCTIONS .............................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	  

	// FIX: no comment header
	public function ForumThreadProcessor($domain) 
	{
		$this->domain   = $domain;
		$this->database = new Database($domain);
		$this->plugin   = $this->loadPlugin();
	}

	// FIX: no comment header
	public function process($html, $url, $parentUrl) 
	{
		$this->myUrl = $url;
		$this->scrapeThreads($html);
	}

	// ========================================================================
	// scrapeThreads
	//    args:  string - HTML of a page of threads
	//    ret:   void
	//    about: Scrapes the threads page for basic thread stats. Calls
	//           scrapeThread (singular) to actually go into a thread. Will have
	//           a delay before entering each thread.
	// ------------------------------------------------------------------------	
	private function scrapeThreads($html)
	{	
		if($this->plugin['threadUrl'] != NULL)
			preg_match_all($this->plugin['threadUrl'], $html, $threadUrls);
		
		if($this->plugin['threadTitle'] != NULL)
			preg_match_all($this->plugin['threadTitle'], $html, $threadTitles);
		
		if($this->plugin['threadNumViews'] != NULL)	
			preg_match_all($this->plugin['threadNumViews'], $html, $numViews);
	
		for($i = 0; $i < sizeof($threadUrls[1]); $i++)
		{
		  	$thisThreadUrl = URL::translateURLBasedOnCurrent($threadUrls[1][$i], $this->myUrl);
			$thisThreadTitle = $threadTitles[1][$i];
			echo 'Translated URL = '.$thisThreadUrl.'</br>';
			
			// check for duplicate
			$q = 'SELECT id
				FROM threads
				WHERE url = "' . mysql_real_escape_string($thisThreadUrl) . '"';
			
			$q = $this->database->query($q);
			
			// only insert if it's new
			if(mysql_num_rows($q) == 0)	
			{	
				$q = 'INSERT INTO threads (title, url)
					VALUES("' . mysql_real_escape_string($thisThreadTitle) . '", 
					"' . mysql_real_escape_string($thisThreadUrl) . '")';
						   
				$this->database->query($q);
				
				$threadId = mysql_insert_id();
			}
			else 
			{
				$q = mysql_fetch_array($q);
				$threadId = $q['id'];
			}
			  
			// some sites don't keep track of this
			if($numViews[1][$i] == '')
				$numViews[1][$i] = 0;
			  
			$q = 'UPDATE threads
				SET views = "' . mysql_real_escape_string($numViews[1][$i]) . '"
				WHERE id = "' . $threadId . '"';
				
			$this->database->query($q);
		}
	}
  
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// HELPER FUNCTIONS ...........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  
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
