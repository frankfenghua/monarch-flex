<?php

// title:  index.php
// author: Ryan Lin
// date:   10/27/08
// about:  Wrapper of the joining process GUI. Loads specific sections into 
//         the main content area. Specify your filename in $_GET['section']
//         without the ".php" part.
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	// for keeping track of user logging in status
	session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	
	<title>Adobe Cat</title>
	
	<link href="reset.css" rel="stylesheet" type="text/css" />
	<link href="base.css" rel="stylesheet" type="text/css" />
	<link href="master.css" rel="stylesheet" type="text/css" />
	
	<script src="ajax.js" type="text/javascript"></script>
	<script src="scriptaculous/lib/prototype.js" type="text/javascript"></script>
	<script src="scriptaculous/src/scriptaculous.js?load=effects" type="text/javascript"></script>
</head>
<body>
<div id="contain">
<?php

	if(!$_SESSION['userId'])
		echo '<a id="homeLink" href="?section=home"></a>';
	else
		echo '<a id="homeLink" href="?section=myAccount"></a>';

	if(!$_GET['section'])
		$section = 'home';
	else
		$section = $_GET['section'];
	
	require_once($section . '.php');

?>
</div>

</body>
</html>
