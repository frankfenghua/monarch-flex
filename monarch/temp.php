<?php

/*

/<div class="comment_time">[\n][\s]+said[\n][\s]+([0-9]+ [a-zA-Z]+[\n][\s]+ago)/




*/

$html = file_get_contents('http://www.designbyhumans.com/forums/show/405884');


// preg_match_all('/<div class="forum-post-title">[\n][\s]+<a href="(.*?)"/', $html, $ret);

// preg_match_all('/<div class="forum-post-title">[\n][\s]+<a href="(?:.*?)">(.*?)</', $html, $ret);

// preg_match_all('/posted[\s]+[\n][\s]+(?:about)? (.*?)[\n]/', $html, $ret);

preg_match_all('/posted[\s]+[\n][\s]+(?:about)? (.*?)[\n]/', $html, $ret);


echo '<pre>';
print_r($ret);
echo '</pre>';


// echo date('m j y', strtotime($ret[1][1]));

?>

