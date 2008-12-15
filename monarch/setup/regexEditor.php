<?php

// title:  regexEditor.php
// author: Ryan Lin
// date:   10/27/08
// about:  Allows user to match regular expressions in realtime with their site
//         and save their regexes.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	$websiteId = mysql_real_escape_string($_GET['websiteId']);
	
	require_once('../database/Database.php');
	
	$database = new Database($websiteId);

	$regexes = $database->fetch('SELECT * FROM regexes');

	
	/*
	for($i = 0; $i < sizeof($regexes); $i++)
		$regexes[$i] = str_replace('"', '&quot;', $regexes[$i]);
	*/
?>

<h1>plugin editor</h1>

<div id="instructions">
	<div>
		<h3>intro</h3>
		This utility helps people match regex's on websites in realtime.
		You must use PHP regex expressions, which are Perl style. 
		Your regexes should look like this: "/myRegex/modifier".
		If your community does not have a certain field, just enter "//" for that regex.
		First load up an HTML page. 
		This will enable threads regex matching.
		After you're done with threads regex matching, posts regex matching will be enabled.
	</div>
	<div>
		<h3>common operators</h3>
		<ul>
			<li><strong>*</strong> - 0 or more occurences</li>
			<li><strong>+</strong> - 1 or more occurences</li>
			<li><strong>?</strong> - 0 or 1 occurence</li>
			<li><strong>{ }</strong> - specific # of occurrences</li>
			<li><strong>( )</strong> - the text you're trying to extract</li>
			<li><strong>[ ]</strong> - set of characters</li>
			<li><strong>^</strong> - not</li>
			<li><strong>\t</strong> - tab</li>
			<li><strong>\n</strong> - newline</li>
			<li><strong>.</strong> - all characters except newline</li>
			<li><strong>s</strong> - modifier that allows . to recognize newlines</li>
			<li><strong>\</strong> - escape characters like /, t, n, r</li>
		</ul>
	</div>
</div>

<form method="post" action="?section=insertRegexes">

	<input type="hidden" name="websiteId" value="<?php echo $websiteId; ?>" />

	<div class="formButton">
		<input type="submit" value="save" />
	</div>
	
	<h2>start page</h2>
	<i>URL of the page to start scraping</i>
	<b>URL:</b>
	<input type="text" name="startPage" id="startPage" value="<?php echo $regexes['startPage']; ?>" />
	<input type="button" value="load" onclick="loadThreadsHtml()" />
	<b>HTML:</b>
	<div id="html">
		<textarea>N/A</textarea>
	</div>
	
	<div id="afterLoadHtml" style="opacity: .2; filter: alpha(opacity=20);">
	
		<h2>link structure</h2>
		<i>how is href="_" used on this site?</i>
		<br />
		<br />
		<input type="radio" name="linkStructure" value="absolute" <?php if($regexes['linkStructure'] == 'absolute') echo 'checked="checked"'; ?> /> absolute
		<br />
		<br />
		<input type="radio" name="linkStructure" value="relative" <?php if($regexes['linkStructure'] == 'relative') echo 'checked="checked"'; ?> /> relative to current
		<br />
		<br />
		<input type="radio" name="linkStructure" value="weirdRelative" <?php if($regexes['linkStructure'] != 'absolute' || $regexes['linkStructure'] != 'relative') echo 'checked="checked"'; ?>/> relative to: 
		<input type="text" style="width: 43em; margin: 0" name="weirdRelativeText" value="<?php if($regexes['linkStructure'] != 'absolute' || $regexes['linkStructure'] != 'relative') echo $regexes['linkStructure']; ?>" />
		
		<h2>next page of threads</h2>
		<i>Regex of the URL to the next page of threads relative to the current page</i>
		<b>input:</b>
		<input type="text" name="nextPageOfThreads" id="nextPageOfThreads" value="<?php echo str_replace('"', '&quot;', $regexes['nextPageOfThreads']); ?>" />
		<input type="button" value="go" onclick="matchRegex('nextPageOfThreads', 'threads')" />
		<b>result:</b>
		<em id="nextPageOfThreadsMatch">N/A</em>
		
		<h2>thread URL</h2>
		<i>URL to enter into a thread</i>
		<b>input:</b>
		<input type="text" name="threadUrl" id="threadUrl" value="<?php echo str_replace('"', '&quot;', $regexes['threadUrl']); ?>" />
		<input type="button" value="go" onclick="loadPostsHtml()" />
		<b>result:</b>
		<em id="threadUrlMatch">N/A</em>
		
		<h2>thread's number of posts</h2>
		<i>The number of replies a thread has.</i>
		<b>input:</b>
		<input type="text" name="threadNumPosts" id="threadNumPosts" value="<?php echo str_replace('"', '&quot;', $regexes['threadNumPosts']); ?>" />
		<input type="button" value="go" onclick="matchRegex('threadNumPosts', 'threads')" />
		<b>result:</b>
		<em id="threadNumPostsMatch">N/A</em>
		
		<h2>thread's number of views</h2>
		<i>The number of times a thread has been viewed.</i>
		<b>input:</b>
		<input type="text" name="threadNumViews" id="threadNumViews" value="<?php echo str_replace('"', '&quot;', $regexes['threadNumViews']); ?>" />
		<input type="button" value="go" onclick="matchRegex('threadNumViews', 'threads')" />
		<b>result:</b>
		<em id="threadNumViewsMatch">N/A</em>
		
		<h2>thread title</h2>
		<i>Title of the thread</i>
		<b>input:</b>
		<input type="text" name="threadTitle" id="threadTitle" value="<?php echo str_replace('"', '&quot;', $regexes['threadTitle']); ?>" />
		<input type="button" value="go" onclick="matchRegex('threadTitle', 'threads')" onmouseup="enablePostsRegex();" />
		<b>result:</b>
		<em id="threadTitleMatch">N/A</em>
		
		<div id="afterThreads" style="opacity: .2; filter: alpha(opacity=20);">
		
			<h2>next page of posts</h2>
			<i>Within a thread, there may be multiple pages. This is the URL of the next page of posts.</i>
			<b>input:</b>
			<input type="text" name="nextPageOfPosts" id="nextPageOfPosts" value="<?php echo str_replace('"', '&quot;', $regexes['nextPageOfPosts']); ?>" />
			<input type="button" value="go" onclick="matchRegex('nextPageOfPosts', 'posts')" />
			<b>result:</b>
			<em id="nextPageOfPostsMatch">N/A</em>
			
			<h2>first post author</h2>
			<i>The thread starter's name</i>
			<b>input:</b>
			<input type="text" name="firstPostAuthor" id="firstPostAuthor" value="<?php echo str_replace('"', '&quot;', $regexes['firstPostAuthor']); ?>" />
			<input type="button" value="go" onclick="matchRegex('firstPostAuthor', 'posts')" />
			<b>result:</b>
			<em id="firstPostAuthorMatch">N/A</em>
			
			<h2>first post time</h2>
			<i>The time that the thread was started (can be in plain English)</i>
			<b>input:</b>
			<input type="text" name="firstPostTime" id="firstPostTime" value="<?php echo str_replace('"', '&quot;', $regexes['firstPostTime']); ?>" />
			<input type="button" value="go" onclick="matchRegex('firstPostTime', 'posts')" />
			<b>result:</b>
			<em id="firstPostTimeMatch">N/A</em>
			
			<h2>first post message</h2>
			<i>The thread starter's message (suggestion: use the multiline modifier "s")</i>
			<b>input:</b>
			<input type="text" name="firstPostMessage" id="firstPostMessage" value="<?php echo str_replace('"', '&quot;', $regexes['firstPostMessage']); ?>" />
			<input type="button" value="go" onclick="matchRegex('firstPostMessage', 'posts')" />
			<b>result:</b>
			<em id="firstPostMessageMatch">N/A</em>
			
			<h2>reply author</h2>
			<i>The name of any author that replied to the thread starter</i>
			<b>input:</b>
			<input type="text" name="replyAuthor" id="replyAuthor" value="<?php echo str_replace('"', '&quot;', $regexes['replyAuthor']); ?>" />
			<input type="button" value="go" onclick="matchRegex('replyAuthor', 'posts')" />
			<b>result:</b>
			<em id="replyAuthorMatch">N/A</em>
			
			<h2>reply time</h2>
			<i>The time of any reply to the thread starter (can be in plain English)</i>
			<b>input:</b>
			<input type="text" name="replyTime" id="replyTime" value="<?php echo str_replace('"', '&quot;', $regexes['replyTime']); ?>" />
			<input type="button" value="go" onclick="matchRegex('replyTime', 'posts')" />
			<b>result:</b>
			<em id="replyTimeMatch">N/A</em>
			
			<h2>reply message</h2>
			<i>The body of any reply to the thread starter (suggestion: use the multiline modifier "s")</i>
			<b>input:</b>
			<input type="text" name="replyMessage" id="replyMessage" value="<?php echo str_replace('"', '&quot;', $regexes['replyMessage']); ?>" />
			<input type="button" value="go" onclick="matchRegex('replyMessage', 'posts')" />
			<b>result:</b>
			<em id="replyMessageMatch">N/A</em>
		
		</div>
		
	</div>
	
	<div class="formButton">
		<input type="submit" value="save" />
	</div>
	
</form>
