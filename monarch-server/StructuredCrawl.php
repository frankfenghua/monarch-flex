<?php
require_once "Url.php";

  // title: StructuredCrawl.php
  // author: Andrew Spencer
  // date:   October 31, 2008
  // about:  Implements a multi-level hierarchical site crawl in the form
  //         of a DFS
  //

  // Class StructuredCrawl
  //  -an object represents a crawling instance
  //  -This class should be used by:
  //     -creating a StructuredCrawl object
  //     -adding all url types that you want to
  //     -calling beginCrawlFromPage to initiate crawling
class StructuredCrawl {

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $url_map;                // Contains a list of URL objects (once added they are not removed)
	private $url_stacks;             // Contains a list of URL objects to be explored
	private $url_types;              // Contains a list of URLType
	private $callbacks;              // Contains a mapping of levels to callback functions
	private $num_levels;             // The number of levels in this crawl
	private $toplevel;               // The toplevel in this crawl
	private $toplevel_pages_crawled; // The current number of pages crawled in this
	private $max_toplevel_pages;     // The current number of pages crawled in this
	private $throttle;               // number of seconds to delay between downloading each page

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// PUBLIC  FUNCTIONS ..........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// ========================================================================
	// StructuredCrawl
	//    args:  int - the depth of levels present in the intended crawl
	//           int - the breadth of pages on the top level to crawl.
	//           int - number of seconds to delay between downloading each page.
	//                 used to prevent our server or the scrapee's server from
	//                 getting over-burdened and possibly get our server IP 
	//                 banned.
	//    ret:   void
	//    about: Constructor. Initializes the object
	// ------------------------------------------------------------------------	
	public function StructuredCrawl($levels, $max_toplevel_pages, $throttle = 0) 
	{
		$this->url_map = array();
		$this->url_stacks = array();
		$this->url_types = array();
		$this->callbacks = array();
		
		for ($level = 0; $level < $levels; $level++) 
		{
			$this->url_map[$level] = array();
			$this->url_stacks[$level] = array();
			$this->url_types[$level] = array();
		}
		
		$this->num_levels = $levels;
		$this->toplevel = 0;
		$this->toplevel_pages_crawled = 0;
		$this->max_toplevel_pages = $max_toplevel_pages;
		$this->throttle = $throttle;
	}


  // ========================================================================
  // beginCrawlFromPage
  //    args:  string - the url of the page to start crawl from
  //    ret:   void
  //    about: Begins crawl. Assumes the parameter url is at level 0
  // ------------------------------------------------------------------------	
  public function beginCrawlFromPage($url_name) {
    $newUrl = $this->addUrl($url_name, 0, NULL); // add level to toplevel
    $next = $this->nextUrl();
    // $next->output();
    $this->crawlFromPage($next);
  }


  // ========================================================================
  // addURLType
  //    args:  string - the perl-style regular expression that matches this url type
  //                    (The match should be a parenthesized group)
  //                    (e.g. '#href="([^"]+)"#)
  //           int -    the crawl level of the page of this type of url. 
  //           int -    the crawl level of the page in which we should look for this type of url 
  //           function - a boolean function which accepts a string url as an argument and
  //                      returns true if this url should be added to the url stack and false
  //                      otherwise (NOT CURRENTLY IMPLEMENTED SO ALL URLS ARE ADDED)
  //                    
  //    ret:   void
  //    about: Adds the url type specified by the parameters to the StructuredCrawl.
  //           If a crawl is begun after adding a url type, then urls matching this
  //           type will be added to the crawl data structures as the crawl progresses. 
  // ------------------------------------------------------------------------	
  public function addURLType($regex, $urlLevel, $foundInLevel, $callback=NULL) {
    array_push($this->url_types[$foundInLevel],new URLType($regex, $urlLevel));
  }

  // ========================================================================
  // addCallback
  //    args:  function - a callback function to call on pages
  //           int -    the page level that the PageProcessor should be used on
  //    ret:   void
  //    about: Maps the parameter level to the PageProcessor object. When pages
  //           of this level are downloaded, the 'process' method of
  //           the PageProcessor object will be called on the contents.
  // ------------------------------------------------------------------------	
  public function addCallback($callback, $level) {
    // var_dump($callback);
    $this->callbacks[$level] = $callback;
  }
  
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// PRIVATE  FUNCTIONS ..........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// ========================================================================
	// crawlFromPage
	//    args:  string - the perl-style regular expression that matches this url type
	//                    (The match should be a parenthesized group)
	//                    (e.g. '#href="([^"]+)"#)
	//                    
	//    ret:   void
	//    about: Recursive function which executes crawling. Shortly, it:
	//      1) Adds all links contained in the page to the appropriate data
	//          structures
	//      2) Does processing on the page's source     
	//      3) Makes recursive call on the next url to crawl. This url is taken
	//          from the bottom-most non-empty stack. If all url stacks are empty
	//          or the crawl count has reached the MAX_TOPLEVEL_PAGES constant then
	//          we terminate.
	//    Something else that could be done:
	//     -- Make this iterative instead of recursive -- DONE
	//           
	// ------------------------------------------------------------------------	
	private function crawlFromPage($url) 
	{
		while($url && ($this->toplevel_pages_crawled < $this->max_toplevel_pages ||
      			$url->getLevel() != $this->toplevel))
		{ 
			$src = $this->downloadUrl($url->getName());
			// $src = file_get_contents($url->getName());
			
			// Look for links matching the url types contained in this level and
			//  add these links to the $url_map and $url_stacks if they are new
			foreach($this->url_types[$url->getLevel()] as $u_type) 
			{
				// Match url strings in the page source
				$urls = $u_type->getMatches($src);
				if($urls) 
				{
					foreach($urls as $u) 
					{
						$u = URL::translateURLBasedOnCurrent($u, $url->getName());
						// echo 'Added url ' . $u . ' with '.$u_type->getLevel().' <br/>';
						$this->addUrl($u,$u_type->getLevel(),$url);
					}
				}
				else if(DEBUG_CRAWL) 
				{
					echo 'No url matches in '.$url->getName().' for ';
					$u_type->output();
				}
			}
		
			// Process page here (analyze content, etc.)
			// echo 'Processing '.$url->getName().'<br/>';
			if(array_key_exists($url->getLevel(), $this->callbacks)) 
			{
				if(DEBUG_CRAWL)
					echo 'Calling back<br/>';
					
				// If this URL has a parent, set parentURLString; otherwise, set it to null
				$parentURLString = $url->getParentURLName();
				
				$this->callbacks[$url->getLevel()]->process($src, $url->getName(), $parentURLString);
			}
		
			// Update members
			$url->setCrawled();
			if($url->getLevel() == $toplevel) 
			{
				if(DEBUG_CRAWL)
					printf('<h3>Finished crawling toplevel page: <a href="%s">%s</a></h3>: <br/>',
						$url->getName(), $url->getName());
						
				$this->toplevel_pages_crawled++;
			}
			else if(DEBUG_CRAWL)
			{
				printf('<h3>Finished crawling non-toplevel page: <a href="%s">%s</a></h3>: <br/>',
					$url->getName(), $url->getName());
			}
		
			// Find next page to crawl and continue crawling
			$url = $this->nextUrl();
			
			// prevent burdening our server or the scrapee's server
			sleep($this->throttle);
		}
		
		if(DEBUG_CRAWL)
		{
			echo 'Max pages crawled <br/>'; 
			if($url) {
				echo 'Stopped on URL ';
				$url->output();
			}
		}
		
		return;
	}

  // ========================================================================
  // addUrl
  //    args:  string - the name of the url to add to data structures
  //           int    - the level of the url to add
  //    ret:   void
  //    about: Adds the url specified to internal data structures if it is
  //           not yet present
  // ------------------------------------------------------------------------	
  private function addUrl($url_name, $url_level, $prev_url) {
    // Add this url if it is not yet in the url_map
    if(!array_key_exists($url_name,$this->url_map[$url_level])) {
	// See Url.php for definition of "Parent URL"
	$parentUrl = NULL;
	if($prev_url && ($url_level == $prev_url->getLevel())) {
		$parentUrlName = $prev_url->getParentURLName();	
	} 
	else {
		$parentUrlName = $url_name;
	}
      $newUrl = new URL($url_name, $url_level, $parentUrlName);
	echo 'Added url '.$url_name.' with parent URL name '.$parentUrlName.'</br>';
      array_push($this->url_stacks[$url_level],$newUrl);
      $this->url_map[$url_level][$url_name] = $newUrl;
      return $newUrl;
    }
    return NULL;
  }

  // ========================================================================
  // nextUrl
  //    ret:   URL - the next url to explore
  //    about: Pops the next url from the lowest level non-empty url stack
  // ------------------------------------------------------------------------	
  private function nextUrl() {
    // Pop from the lowest level non-empty url stack
    // print_r($this->url_stacks);
    for ( $cur = $this->num_levels - 1; $cur >= 0; --$cur ) {
      if(sizeof($this->url_stacks[$cur]) > 0) {
	 return array_shift($this->url_stacks[$cur]);
      }
    }
    return NULL;
  }

  // =======================================================================
  // downloadUrl
  //    ret:  String - the contents of the URL
  //    about: downloads the page contained at the parameter URL and returns
  //           the contents as a string
  // -----------------------------------------------------------------------
  private function downloadUrl($urlString) {
    $ch = curl_init(); 

    curl_setopt($ch, CURLOPT_URL, $urlString);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0"); // impersonate browser
    return curl_exec($ch);
  }

}
?>
