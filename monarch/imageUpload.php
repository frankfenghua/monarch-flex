<?php

$newfilename = $_POST['id'] . '.jpg';
move_uploaded_file($_FILES['Filedata']['tmp_name'], './tmp/' . $_FILES['Filedata']['name']);
rename('./tmp/' . $_FILES['Filedata']['name'], './images/' . $newfilename);

echo 'Successfully Uploaded';

require_once('PhotoManip.php');
$photoManip = new PhotoManip('./images/' . $newfilename);
$photoManip->thumbnail(64, 64);

?>
