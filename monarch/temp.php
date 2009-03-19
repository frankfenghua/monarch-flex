<?php

$html = file_get_contents('http://www.cnn.com/2009/POLITICS/03/18/aig.bonuses.congress/index.html');

/*

<b>(CNN)</b> -- Senate Banking committee Chairman Christopher Dodd told CNN Wednesday that he was responsible for language added to the federal stimulus bill to make sure that already-existing contracts for bonuses at companies receiving federal bailout money were honored.</p> <!--startclickprintexclude-->






*/

// preg_match_all('/<h3>\n<a href="(.*?)"/', $html, $res);

// preg_match_all('/<h3>\n<a href=".*?">(.*?)</', $html, $res);

//preg_match_all('/<p class="cnnAttribution">.*? (.*?) contributed/', $html, $res);

// preg_match_all('/([0-9]{4}\/[0-9]{2}\/[0-9]{2})\//', $html, $res);

preg_match_all('/<div id="cnnHighLightTrigger">(.*?)<\/div>/s', $html, $res);

print_r($res);



?>

