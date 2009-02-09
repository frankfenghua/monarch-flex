<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors','1');

require_once '../constants.php';
require_once '../StructuredCrawl.php';
require_once '../ForumPostProcessor.php';
require_once '../ForumThreadProcessor.php';
require_once '../database/Database.php';

$name = $_GET["name"];
$topLevel = $_GET["topLevel"];
$postProcessor = new ForumPostProcessor($name);
$threadProcessor = new ForumThreadProcessor($name);

$crawl = new StructuredCrawl(CRAWL_NUM_LEVELS, $topLevel, CRAWL_THROTTLE);
$db = new Database($name);
  
// Get regular expressions from database and set the crawl parameters accordingly
$q = "SELECT startPage, threadUrl, nextPageOfThreads, nextPageOfPosts from regexes";
$result = mysql_fetch_assoc($db->query($q));
// $crawl->addURLType('/class="forum_title" href="([^"]+)"/', CRAWL_POST_LEVEL, CRAWL_THREAD_LEVEL);
$crawl->addURLType($result["threadUrl"], CRAWL_POST_LEVEL, CRAWL_THREAD_LEVEL);
$crawl->addURLType($result["nextPageOfThreads"], CRAWL_THREAD_LEVEL, CRAWL_THREAD_LEVEL);
$crawl->addURLType($result["nextPageOfPosts"], CRAWL_POST_LEVEL, CRAWL_POST_LEVEL);

$crawl->addCallback($postProcessor, CRAWL_POST_LEVEL); 
$crawl->addCallback($threadProcessor, CRAWL_THREAD_LEVEL);
// $crawl->beginCrawlFromPage('http://threadless.com/blogs/blogs');
$crawl->beginCrawlFromPage($result["startPage"]);
?>
