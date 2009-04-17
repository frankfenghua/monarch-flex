<?php

$date = $_POST['showDate'];
$timeStamp = strtotime($date);

$query = 'SELECT * 
	FROM products 
	WHERE date BETWEEN ' . $timeStamp . ' AND ' . ($timeStamp + 86000);

$database->query('SELECT * FROM products WHERE date BETWEEN ' . $timeStamp . ' AND ' . $timeStamp)

?>