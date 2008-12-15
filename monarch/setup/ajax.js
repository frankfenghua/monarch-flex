// title:  ajax.js
// author: Ryan Lin
// date:   10/17/08
// about:  AJAX functions for having realtime regular expression matching in 
//         the plugin editor
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

var doneWith = "nothing"; // keeps track of what the user has done on this page
                          // so we can reveal sections of the page at the right
                          // time.

// ============================================================================
// ajax
//    args:  string - server script file with or without parameters appended to
//                    to the end
//           string - id name of the element you want to insert the server
//                    response. Set to null if this is a behind the scenes 
//                    server script with no visual effects
//    ret:   void
//    about: standard AJAX GET roundtrip
// ----------------------------------------------------------------------------
function ajax(serverScript, insertId) 
{
	var httpRequest;

	// Non IE browsers
	if (window.XMLHttpRequest) 
	{ 
		httpRequest = new XMLHttpRequest();
		if (httpRequest.overrideMimeType) 
		{
			httpRequest.overrideMimeType('text/xml');
		}
	} 
	// IE browsers
	else if (window.ActiveXObject)
	{ 
		try 
		{
			httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} 
		catch (e) 
		{
			try 
			{
				httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} 
			catch (e) 
			{
				alert("Your browser is so old that it can't run this website");
			}
		}
	}

	// insert the server's response into the site
	httpRequest.onreadystatechange = function() 
	{
		if (httpRequest.readyState == 4) 
		{
			if (httpRequest.status == 200 && insertId != null)
				document.getElementById(insertId).innerHTML = httpRequest.responseText;
			else
				alert('There was a problem with the AJAX request.');
		}
		else if(insertId != null)
			document.getElementById(insertId).innerHTML = '<img src="images/loading.gif" alt="loading..." />';
	};
	
	httpRequest.open('GET', serverScript, true);
	httpRequest.send('');

}

// ============================================================================
// loadThreadsHtml
//    args:  none
//    ret:   void
//    about: Loads the start page's HTML into a textarea. Enables the regex 
//           matching for threads. Will not reveal twice if user clicks twice.
// ----------------------------------------------------------------------------
function loadThreadsHtml()
{
	var url = document.getElementById('startPage').value;
	ajax("loadThreadsHtml.php?url=" + url, "html");

	if(doneWith == "nothing")
	{
		new Effect.Appear("afterLoadHtml", {from: 0.2});
		doneWith = "loadedThreadsHtml";
	}
}

// ============================================================================
// loadPostsHtml
//    args:  none
//    ret:   void
//    about: Stores a single thread's HTML in our folder, so future posts regex
//           matching can use it. Also simultaneously does regex matching only
//           for the threadUrl.
// ----------------------------------------------------------------------------
function loadPostsHtml()
{
	var regex = document.getElementById("threadUrl").value;
	
	var url = document.getElementById('threadUrlMatch').innerHTML;
	ajax("loadPostsHtml.php?&regex=" + escape(regex) , "threadUrlMatch");
}

// ============================================================================
// matchRegex
//    args:  string - which regex are we matching?
//           string - Which file are you doing the matching with?
//                    "threads" | "posts"
//    ret:   void
//    about: Matches the regex against the HTML and outputs it.
// ----------------------------------------------------------------------------
function matchRegex(regexName, file)
{
	var regex = document.getElementById(regexName).value;
	ajax("matchRegex.php?regex=" + escape(regex) + "&file=" + file, regexName + "Match");
}

// ============================================================================
// enablePostsRegex
//    args:  none
//    ret:   void
//    about: Reveals regex matching form for posts after the user is done with
//           typing in regexes for threads. Will only reveal them once.
// ----------------------------------------------------------------------------
function enablePostsRegex()
{
	if(doneWith == "loadedThreadsHtml")
	{
		new Effect.Appear("afterThreads", {from: 0.2});	
		doneWith = "typedThreadsRegexes";
	}
}