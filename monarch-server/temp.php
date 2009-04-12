<?php

/*

/<span class="fontsize2 author">\n[\s]{2}<span style="color: #00681C;">(.*?)<\/span>/
<span class="fontsize2 author">

  <span style="color: #790619;">Major Debacle</span>








*/

$html = file_get_contents('http://groups.google.com/group/alt.politics.usa/browse_thread/thread/419b783eccd25a13#');

preg_match_all('/<span class="fontsize0">&nbsp;<span class="(?:.*?)" id="oh_l">More options<\/span><\/span>\n  <span class="fontsize2">\n  (.*?)\n/', $html, $res);

echo html_entity_decode('&lt;b&gt;bo&nbsp;ld&lt;/b&gt;');

//print_r($res);

// print_r(strtotime($res[1][0]));

?>