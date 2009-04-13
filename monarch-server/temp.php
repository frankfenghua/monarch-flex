<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$html = file_get_contents('http://groups.google.com/group/alt.politics.usa/browse_thread/thread/419b783eccd25a13#');

preg_match_all('/<span class="fontsize0">&nbsp;<span class="(?:.*?)" id="oh_l">More options<\/span><\/span>\n  <span class="fontsize2">\n  (.*?)\n/', $html, $result);


echo '$result[1][0] = ' . $result[1][0];

echo '<br />';

$temp = html_entity_decode($result[1][0]);

// These characters are produced when there is a '&nbsp'
$temp = str_replace(chr(160), ' ',$temp);
$temp = str_replace(chr(194), '',$temp);
echo 'temp = '. $temp . '<br />';
echo '<br />';

echo 'strtotime($temp) = ' . strtotime($temp);
echo '<br />';
echo 'strtotime("Apr 10, 8:19 am") = ' . strtotime("Apr 10, 8:19 am");
echo '<br />';
echo 'strtotime(utf8_encode(html_entity_decode($result[1][0]))) = ' . strtotime(utf8_encode(html_entity_decode($result[1][0])));

function prStr($str) {
	for($i = 0; $i < strlen($str); $i++) {
		echo 'Pos '.$i.'= '.ord($str[$i]).'<br />';
	}
}

?>
