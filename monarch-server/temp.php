<?php

/*

<h2 class="thumb clearfix">

          <a href="/account/profile_image/SarahPalin"><img alt="" border="0" height="73" id="profile-image" src="http://s3.amazonaws.com/twitter_production/profile_images/88898601/sarah-palin-cropped_bigger.jpg" valign="middle" width="73" /></a>
    
    SarahPalin
  </h2>

/1578905956" class="entry-date" rel="bookmark"><span class="published">about 1 hour ago</span></a> <span>from <a href="http://www.hootsuite.com">HootSuite</a></span>

*/


$html = file_get_contents('http://twitter.com/SarahPalin');

preg_match_all('#<li class="hentry status u-(?:.*?)" id="status_[0-9]+"><span class="status-body"><span class="entry-content">(.*?)</span>#s', $html, $r);

print_r($r);


// echo strtotime('1 hour ago');

?>