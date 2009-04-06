<?php
$MAXIMUM_FILESIZE = 1024 * 200; // 200KB
echo exif_imagetype($_FILES['Filedata']);
$newfilename = $_POST['id'].'.jpg';
move_uploaded_file($_FILES['Filedata']['tmp_name'], "./tmp/".$_FILES['Filedata']['name']);
$type = exif_imagetype("./tmp/".$_FILES['Filedata']['name']);
$size = getimagesize("./tmp/".$_FILES['Filedata']['name']);

if ($type == 2 && $size[0] == 64 && $size[1] == 64)
{
	echo "Upload successful";
    rename("./tmp/".$_FILES['Filedata']['name'], "./images/".$newfilename);
}
else
{
	if($type == 2) {
		echo "Upload failed: 64x64 image required";
	}
	else {
		echo "Upload failed: JPEG image required";
	}
    unlink("./tmp/".$_FILES['Filedata']['name']);
}
?>
