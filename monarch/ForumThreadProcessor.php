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

class ForumThreadProcessor implements Processor {

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

  private $database;     // connection to the master DB or specific community's DB
  private $domain;       // name of the community
  private $plugin;       // regular expressions for how to scrape this community

  public function ForumThreadProcessor($domain) {
    $this->domain       = $domain;
    $this->database     = new Database($domain);
    $this->plugin       = $this->loadPlugin();
  }

  public function process($html) {
    $this->scrapeThreads($html);
  }

  // ========================================================================
  // scrapeThreads
  //    args:  string - HTML of a page of threads
  //    ret:   void
  //    about: Scrapes the threads page for basic thread stats. Calls
  //           scrapeThread (singular) to actually go into a thread. Will have
  //           a delay before entering each thread.
  //    fix:   * Checking thread title for duplicates is not robust since 
  //             people can edit their titles. 
  //           * New replies can be added to a thread which we've alrady 
  //             scraped for. Currently, we would not be checking those new
  //             replies.
  // ------------------------------------------------------------------------	
  private function scrapeThreads($html)
  {	
    if($this->plugin['threadUrl'] != NULL)
      preg_match_all($this->plugin['threadUrl'], $html, $threadUrl);
    
    if($this->plugin['threadTitle'] != NULL)
      preg_match_all($this->plugin['threadTitle'], $html, $threadTitle);
    
    if($this->plugin['threadNumPosts'] != NULL)
      preg_match_all($this->plugin['threadNumPosts'], $html, $numPosts);
    
    if($this->plugin['threadNumViews'] != NULL)	
      preg_match_all($this->plugin['threadNumViews'], $html, $numViews);
    
    for($i = 0; $i < sizeof($threadUrl[1]); $i++)
      {
	// check for duplicate
	$q = 'SELECT id
			      FROM threads
			      WHERE url = "' . $threadUrl[1][$i] . '"';
	
	$q = $this->database->query($q);
	
	// only insert if it's new
	if(mysql_num_rows($q) == 0)	
	  {		
	    /* $q = 'INSERT INTO threads (posts, views, title)
				      VALUES("' . $numPosts[1][$i]    . '",
				             "' . $numViews[1][$i]    . '", 
				             "' . $threadTitle[1][$i] . '")';
	    */
	    $q = 'INSERT INTO threads (posts, views, title, url)
				      VALUES("' . $numPosts[1][$i]    . '",
				             "' . 0    . '", 
				             "",
                                             "' . $threadUrl[1][$i]   . '")';
				            
				           
	    
	    $this->database->query($q);
	    
	  }
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
    $q = 'SELECT startPage, linkStructure, nextPageOfThreads, nextPageOfPosts, threadUrl, 
		      threadNumPosts, threadNumViews, threadTitle, firstPostAuthor, firstPostTime,
		      firstPostMessage, replyAuthor, replyTime, replyMessage 
		      FROM regexes
		      WHERE id = 0';
    
    return $this->database->fetch($q);
  }
}
?>
