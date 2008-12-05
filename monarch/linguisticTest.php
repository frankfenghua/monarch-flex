<?php

// TITLE:  linguisticTest.php
// TYPE:   excutable
// AUTHOR: Ryan Lin
// DATE:   12/04/2008
// ABOUT:  Testing the functions in Linguistics.php
// ================================================================================

require_once('Linguistics.php');

$linguistics = new Linguistics();

$a = 'Adobe is bad. It sucks. I hate it.';
$b = 'Adobe is not bad. It does not suck. I don\'t hate it';
$g = 'Adobe is good. It is great. I love it.';
$h = 'Adobe is very good. It is so great. I love it.';
$c = 'My spelleeng iss horibel. I am nott a trustwoorthy persun';
$d = 'My spelling is good. My posts can be trusted';
$e = 'I am bad at " punctuation ) and (cannot) be "trusted"';
$f = 'I am good at "punctuation" and (can) be trusted';
$i = 'i rush when typing. often i don\'t capitalize. don\'t trust me.';
$j = 'I am a careful writer! I take my time to capitalize. Truse me!';

echo '<h1>linguistics test</h1>';
echo '<h2>trash talking</h3>';
printf('<i>%s</i><br /><br /><b>goodness of "adobe":</b> %f', $a, $linguistics->goodness('adobe', $a));
echo '<h2>negated trash talking</h3>';
printf('<i>%s</i><br /><br /><b>goodness of "adobe":</b> %f', $b, $linguistics->goodness('adobe', $b));
echo '<h2>ass kissing</h3>';
printf('<i>%s</i><br /><br /><b>goodness of "adobe":</b> %f', $g, $linguistics->goodness('adobe', $a));
echo '<h2>severe ass kissing</h3>';
printf('<i>%s</i><br /><br /><b>goodness of "adobe":</b> %f', $h, $linguistics->goodness('adobe', $h));
echo '<h2>misspelling</h3>';
printf('<i>%s</i><br /><br /><b>english proficiency:</b> %f', $c, $linguistics->englishProficiency($c));
echo '<h2>correct spelling</h3>';
printf('<i>%s</i><br /><br /><b>english proficiency:</b> %f', $d, $linguistics->englishProficiency($d));
echo '<h2>bad punctuation</h3>';
printf('<i>%s</i><br /><br /><b>english proficiency:</b> %f', $e, $linguistics->englishProficiency($e));
echo '<h2>bad punctuation</h3>';
printf('<i>%s</i><br /><br /><b>english proficiency:</b> %f', $i, $linguistics->englishProficiency($i));
echo '<h2>good capitalization</h3>';
printf('<i>%s</i><br /><br /><b>english proficiency:</b> %f', $j, $linguistics->englishProficiency($j));

?>
