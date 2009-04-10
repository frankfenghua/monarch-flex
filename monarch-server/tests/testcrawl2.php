<?php
require_once 'StructuredCrawl.php';
require_once 'Processor.php';
// require_once 'ThreadlessProcessor.php';

// $processor = new ThreadlessProcessor('threadless');

ini_set('display_errors', 1);
error_reporting(E_ALL);
$crawl = new StructuredCrawl(2);
$processor = new Processor("reddit");
  
$crawl->addURLType('#a id="comment[^"]+" href="([^"]+)"#',1,0);
$crawl->addCallback($processor, 1);
// $crawl->addURLType('/class="pagea " href="([^"]+)"/',0,0);
// $crawl->addCallback($processor->processThreadLevel,0);
// $crawl->addCallback($processor->processPostLevel,1);
$crawl->beginCrawlFromPage('http://reddit.com');
?>
