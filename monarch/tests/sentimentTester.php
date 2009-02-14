<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sentiment Tester</title>
<style type="text/css">
<!--
	th {
		text-align: right;
		vertical-align: top;
	}
	
	th, td {
		padding: 0.6em;
	}
	
	textarea, input[type="text"] {
		width: 40em;
		border: 1px solid #999;
		border-top: 1px solid #000;
		background: #ffffe5;
		padding: 0.4em;
	}
	
	textarea:focus, input[type="text"]:focus {
		background: #ffffcc;
	}
	
	textarea {
		height: 20em;
	}
	
	h1 {
		text-align: center;
	}
-->
</style>
</head>

<body>

<h1>Sentiment Tester</h1>

<?php
	require_once('../Linguistics.php');

	if(!$_POST['text'] || !$_POST['keyword'])
	{
?>
		<form action="sentimentTester.php" method="post">
			<table>
				<tr>
					<th>
						block of text:
					</th>
					<td>
						<textarea name="text"></textarea>
					</td>
				</tr>
				<tr>
					<th>
						a keyword from the text:
					</th>
					<td>
						<input type="text" name="keyword" />
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="submit" value="analyze" />
					</td>
				</tr>
			</table>
		</form>
<?php
	}
	else
	{
		$linguistics = new Linguistics();
	
		printf('<h3>original text</h3>%s<hr />', $_POST['text']);
		printf('<h3>goodness of "%s": %f</h3>', $_POST['keyword'], $linguistics->goodness($_POST['keyword'], $_POST['text']));
	}
?>
		
		
		
</body>
</html>
