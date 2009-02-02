<?php

// TITLE:  constants.php
// TYPE:   data file
// AUTHOR: Ryan Lin, Andrew Spencer
// DATE:   12/29/2008
// ABOUT:  Some values used throughout this program do not have intuitive meanings,
//         so we define these constants with meaningful variable names here.
// ================================================================================

define('CRAWL_THREAD_LEVEL', 0);                // FIX: don't know
define('CRAWL_POST_LEVEL', 1);                  // FIX: don't know
define('CRAWL_NUM_LEVELS', 2);                  // the depth of levels present in the intended crawl
define('CRAWL_MAX_TOPLEVEL_PAGES_TO_CRAWL', 5); // the breadth of pages on the top level to crawl.
define('CRAWL_THROTTLE', 1);                    // number of seconds to delay between downloading each page

define('REGEX_ENGLISH_WORD', 'a-zA-Z0-9');
define('REGEX_SENTENCE_WO_PUNCT', '^\?\.!');
define('REGEX_INPROPER_FIRST_CHAR_OF_WORD', 'a-z0-9\W');
define('REGEX_SANDWICHED_PERIOD', '[a-zA-Z0-9]\.[a-zA-Z0-9]');
define('REGEX_DOMAIN_SUFFIXES', 'com|net|info|org|me|tv|mobi|biz|us|ca|asia|ws|ag|am|at|be|cc|cn|de|eu|fm|fm|gs|jobs|jp|ms|nu|co|nz|tc|tw|idv|uk|vg');

define('POST_HASH_LENGTH', 8);

?>