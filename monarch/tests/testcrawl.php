<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors','1');

require_once '../constants.php';
require_once '../StructuredCrawl.php';
require_once '../ForumPostProcessor.php';
require_once '../ForumThreadProcessor.php';

$postProcessor = new ForumPostProcessor('threadless');
$threadProcessor = new ForumThreadProcessor('threadless');

$crawl = new StructuredCrawl(CRAWL_NUM_LEVELS, CRAWL_THROTTLE);
// $crawl = new StructuredCrawl(CRAWL_NUM_LEVELS, CRAWL_MAX_TOPLEVEL_PAGES_TO_CRAWL);
  
$crawl->addURLType('/class="forum_title" href="([^"]+)"/', CRAWL_POST_LEVEL, CRAWL_THREAD_LEVEL);
$crawl->addURLType('/class="pagea " href="([^"]+)"/', CRAWL_THREAD_LEVEL, CRAWL_THREAD_LEVEL);
$crawl->addURLType('/class="pagea" href="([^"]+)"/', CRAWL_POST_LEVEL, CRAWL_POST_LEVEL);

// $crawl->addCallback($postProcessor, CRAWL_POST_LEVEL); 
// $crawl->addCallback($threadProcessor, CRAWL_THREAD_LEVEL);

// Example use of definePageLimit:
//  This sets the number of threads to crawl to 2
$crawl->definePageLimit(CRAWL_POST_LEVEL, 2);
$crawl->beginCrawlFromPage('http://threadless.com/blogs/blogs');

?>
