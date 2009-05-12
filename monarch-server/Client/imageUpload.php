<?php

// create the folder for storing thumbnails
if(!file_exists('../../images/')
	mkdir('../../images');

// TODO: Andrew, what's all this shit?
$newfilename = $_POST['id'] . '.jpg';
move_uploaded_file($_FILES['Filedata']['tmp_name'], './tmp/' . $_FILES['Filedata']['name']);
rename('./tmp/' . $_FILES['Filedata']['name'], '../../images/' . $newfilename);

// notify GUI
echo 'Successfully Uploaded';

// take the uploaded image and make a thumbnail out of it
require_once('PhotoManip.php');
$photoManip = new PhotoManip('../../images/' . $newfilename);
$photoManip->thumbnail(64, 64);

?>
