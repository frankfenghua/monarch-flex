<?php

$html = file_get_contents('http://groups.google.com/group/alt.politics.usa/browse_thread/thread/419b783eccd25a13#');

preg_match_all('/<span class="fontsize0">&nbsp;<span class="(?:.*?)" id="oh_l">More options<\/span><\/span>\n  <span class="fontsize2">\n  (.*?)\n/', $html, $result);


echo '$result[1][0] = ' . $result[1][0];

echo '<br />';

echo 'html_entity_decode($result[1][0]) = ' . html_entity_decode($result[1][0]); 

echo '<br />';

echo 'strtotime(html_entity_decode($result[1][0])) = ' . strtotime(html_entity_decode($result[1][0]));

echo '<br />';

echo 'strtotime("Apr 10, 8:19 am") = ' . strtotime("Apr 10, 8:19 am");


?>