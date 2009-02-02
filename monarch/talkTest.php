<?php

require('database/Database.php');

function signColor($rating)
{
	if($rating > 0)
		return 'green';
	else if($rating < 0)
		return 'red';
	else
		return 'gray';
}

$database = new Database('threadless');

$a = 'SELECT *
	FROM keywords';
	
$a = $database->query($a);

while($word = mysql_fetch_array($a))
{
	$keyword = $word['word'];
	$keywordId = $word['id'];
	
	echo '<hr>';
	
	// ============================================================================
	
	$q = 'SELECT t.url, t.title, s.goodness AS rating
		FROM threads AS t, threadstats AS ts, stats AS s
		WHERE t.id = ts.thread
		AND ts.keyword = ' . $keywordId .'
		AND ts.stat = s.id
		ORDER BY rating
		DESC
		LIMIT 5';
		
	$q = $database->query($q);
	
	printf('<center><h1>talk about "%s"</h1></center>', $keyword);
	printf('<h3>Top 5 threads that talk well about "%s"</h3>', $keyword);
	echo '<table>';
	
	while($thread = mysql_fetch_array($q))
		printf('<tr><td><a href="http://www.threadless.com%s">%s</a></td><td><i>goodness rating: <font color="%s">%f</font></i></td></tr>', 
			$thread['url'], $thread['title'], signColor($thread['rating']), $thread['rating']);
	
	echo '</table>';
	
	// ============================================================================
	
	$q = 'SELECT t.url, t.title, s.goodness AS rating
		FROM threads AS t, threadstats AS ts, stats AS s
		WHERE t.id = ts.thread
		AND ts.keyword = ' . $keywordId .'
		AND ts.stat = s.id
		ORDER BY rating
		ASC
		LIMIT 5';
		
	$q = $database->query($q);
	
	printf('<h3>Top 5 threads that talk badly about "%s"</h3>', $keyword);
	echo '<table>';
	
	while($thread = mysql_fetch_array($q))
		printf('<tr><td><a href="http://www.threadless.com%s">%s</a></td><td><i>goodness rating: <font color="%s">%f</font></i></td></tr>', 
			$thread['url'], $thread['title'], signColor($thread['rating']), $thread['rating']);
	
	echo '</table>';
	
	// ============================================================================
	
	$q = 'SELECT t.url, t.title, s.count
		FROM threads AS t, threadstats AS ts, stats AS s
		WHERE t.id = ts.thread
		AND ts.keyword = ' . $keywordId .'
		AND ts.stat = s.id
		ORDER BY count
		DESC
		LIMIT 5';
		
	$q = $database->query($q);
	
	printf('<h3>Top 5 threads that talk a lot about "%s"</h3>', $keyword);
	echo '<table>';
	
	while($thread = mysql_fetch_array($q))
		printf('<tr><td><a href="http://www.threadless.com%s">%s</a></td><td><i>count: %d</i></td></tr>', 
			$thread['url'], $thread['title'], $thread['count']);
	
	echo '</table>';
	
	// ============================================================================
	
	$q = 'SELECT u.name, u.url, s.goodness AS rating
		FROM users AS u, userstats AS us, stats AS s
		WHERE u.id = us.user
		AND us.keyword = ' . $keywordId .'
		AND us.stat = s.id
		ORDER BY rating
		DESC
		LIMIT 5;';
		
	$q = $database->query($q);
	
	printf('<h3>Top 5 people that talk well about "%s"</h3>', $keyword);
	echo '<table>';
	
	while($thread = mysql_fetch_array($q))
		printf('<tr><td><a href="http://www.threadless.com%s">%s</a></td><td><i>goodness: <font color="%s">%f</font></i></td></tr>', 
			$thread['url'], $thread['name'], signColor($thread['rating']), $thread['rating']);
	
	echo '</table>';
	
	// ============================================================================
	
	$q = 'SELECT u.name, u.url, s.goodness AS rating
		FROM users AS u, userstats AS us, stats AS s
		WHERE u.id = us.user
		AND us.keyword = ' . $keywordId .'
		AND us.stat = s.id
		ORDER BY rating
		ASC
		LIMIT 5;';
		
	$q = $database->query($q);
	
	printf('<h3>Top 5 people that talk badly about "%s"</h3>', $keyword);
	echo '<table>';
	
	
	
	while($thread = mysql_fetch_array($q))
		printf('<tr><td><a href="http://www.threadless.com%s">%s</a></td><td><i>goodness: <font color="%s">%f</font></i></td></tr>', 
			$thread['url'], $thread['name'], signColor($thread['rating']), $thread['rating']);
	
	echo '</table>';
	
	// ============================================================================
	
	$q = 'SELECT u.name, u.url, s.count
		FROM users AS u, userstats AS us, stats AS s
		WHERE u.id = us.user
		AND us.keyword = ' . $keywordId .'
		AND us.stat = s.id
		ORDER BY count
		DESC
		LIMIT 5;';
		
	$q = $database->query($q);
	
	printf('<h3>Top 5 people that talk a lot about "%s"</h3>', $keyword);
	echo '<table>';
	
	while($thread = mysql_fetch_array($q))
		printf('<tr><td><a href="http://www.threadless.com%s">%s</a></td><td><i>count: %d</i></td></tr>', 
			$thread['url'], $thread['name'], $thread['count']);
	
	echo '</table>';
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

$q = 'SELECT SUM(s.goodness) AS totalGoodness, l.baseUrl
	FROM stats AS s, linkstats AS ls, links AS l
	WHERE l.id = ls.link
	AND ls.stat = s.id
	GROUP BY l.baseUrl
	ORDER BY totalGoodness 
	DESC
	LIMIT 10';
	
$q = $database->query($q);

echo '<hr>';
echo '<center><h1>websites</h1></center>';
echo '<h3>Favorable websites</h3>';
echo '<table>';

while($link = mysql_fetch_array($q))
	printf('<tr><td><a href="http://www.%s">%s</a></td><td><i>total goodness: <font color="%s">%f</font></i></td></tr>', 
		$link['baseUrl'], $link['baseUrl'], signColor($link['totalGoodness']), $link['totalGoodness']);

echo '</table>';

$q = 'SELECT SUM(s.goodness) AS totalGoodness, l.baseUrl
	FROM stats AS s, linkstats AS ls, links AS l
	WHERE l.id = ls.link
	AND ls.stat = s.id
	GROUP BY l.baseUrl
	ORDER BY totalGoodness 
	ASC
	LIMIT 10';
	
$q = $database->query($q);

echo '<h3>Bad websites</h3>';
echo '<table>';

while($link = mysql_fetch_array($q))
	printf('<tr><td><a href="http://www.%s">%s</a></td><td><i>total goodness: <font color="%s">%f</font></i></td></tr>', 
		$link['baseUrl'], $link['baseUrl'], signColor($link['totalGoodness']), $link['totalGoodness']);

echo '</table>';
	

?>