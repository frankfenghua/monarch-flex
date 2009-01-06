<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors','1');

require_once '../constants.php';
require_once '../StructuredCrawl.php';
require_once '../ForumPostProcessor.php';
require_once '../ForumThreadProcessor.php';

$postProcessor = new ForumPostProcessor('threadless');
$threadProcessor = new ForumThreadProcessor('threadless');

$crawl = new StructuredCrawl(CRAWL_NUM_LEVELS, CRAWL_MAX_TOPLEVEL_PAGES_TO_CRAWL, CRAWL_THROTTLE);
  
$crawl->addURLType('/class="forum_title" href="([^"]+)"/', CRAWL_POST_LEVEL, CRAWL_THREAD_LEVEL);
$crawl->addURLType('/class="pagea " href="([^"]+)"/', CRAWL_THREAD_LEVEL, CRAWL_THREAD_LEVEL);
$crawl->addURLType('/class="pagea" href="([^"]+)"/', CRAWL_POST_LEVEL, CRAWL_POST_LEVEL);

$crawl->addCallback($postProcessor, CRAWL_POST_LEVEL); 
$crawl->addCallback($threadProcessor, CRAWL_THREAD_LEVEL);
$crawl->beginCrawlFromPage('http://threadless.com/blogs/blogs');

?>
