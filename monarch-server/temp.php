<?php

/*
  
	<!-- google_ad_section_start -->
	
		<p><img src="http://cache.gawker.com/assets/images/gizmodo/2009/04/Blender_Art_1.jpg"  width="504" height="315" style="display:block;" />The opening question in any serious empirical research is "<a class="autolink" title="Click here to read more posts tagged WILL IT BLEND" href="http://gizmodo.com/tag/will-it-blend/">Will it blend</a>?" But here's a German artist who felt that her art should begin with a slightly different question: "<i>How</i> will it blend?"</p>
<p>In her series <i>Nicht Klotzen, Sondern Kleckern</i> Sarah Illenberger assembled a collection of slick European blenders, <a class="autolink" title="Click here to read more posts tagged HAND MIXERS" href="http://gizmodo.com/tag/hand-mixers/">hand mixers</a> and stick blenders and put each one to use as a brush in a different painting. I'm no art critic, but I'm gonna say the <a class="tagautolink autolink" title="Click here to read more posts tagged HAND MIXERS" title="Click here to read more posts tagged HAND MIXERS" href="http://gizmodo.com/tag/hand-mixers/">hand mixers</a> were the most aesthetically pleasing, while the big tabletop Bosch was pretty much a bust. The real question is, what gear should Illenberger use to paint with next? [<a href="http://www.todayandtomorrow.net/2009/04/23/nicht-klotzen-sondern-kleckern/">Today and Tomorrow</a>]</p>

<p><img src="http://cache.gawker.com/assets/images/gizmodo/2009/04/Blender_Art_3.jpg"  width="504" height="315" style="display:block;" /><br clear="all">
<br>
<img src="http://cache.gawker.com/assets/images/gizmodo/2009/04/Blender_Art_2.jpg"  width="504" height="315" style="display:block;" /><br clear="all"></p>

								<!-- google_ad_section_end -->   

*/

$html = file_get_contents('http://i.gizmodo.com/5225255/blenders-used-for-art-not-science');

preg_match_all('#<!-- google_ad_section_start -->(.*?)<!-- google_ad_section_end -->   #s', $html, $r);

print_r($r);

?>