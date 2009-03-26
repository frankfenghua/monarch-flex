<?php

include('database/Database.php');
$db = new Database('master');

$q = 'update badwords set word = "suicide" where word = ' . mysql_real_escape_string('0; drop table badwords');

echo $q;

$db->query($q);

?>

