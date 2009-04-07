<?php

// ===========================================================================
// exif_imagetype
//    args:  string - path to image file
//    ret:   constant telling you what file type it is
//    about: couldn't figure out how to install it on my WAMP, so I defined it
//           here.
// ---------------------------------------------------------------------------
if(!function_exists('exif_imagetype')) 
{
	function exif_imagetype ($filename) 
	{
		if((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) 
			return $type;
		else
			return false;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 
// TITLE:   imageUpload
// TYPE:    class
// CREATED: Apr. 7, 2009
// AUTHOR:  Andrew Spencer, Ryan Lin
// ABOUT:   Take an original image, manipulate it, then save it as a new image
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 

class PhotoManip
{

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// MEMBER FIELDS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	private $fileType;          // int - file type of the image
	private $originalFileName;  // string - file name of the original image
	private $finalFileName;     // string - file name of the final image
	private $originalHeight;    // int - height of the original image
	private $originalWidth;     // int - width of the original image
	private $originalResource;  // object - PHP's wrapper to manipulate the original image
	private $finalResource;     // object - PHP's wrapper to store the final image
	
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// PUBLIC METHODS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// ========================================================================
	// PhotoManip
	//    args:  string - path to the original image
	//           string - path to where we will output the manipulated image. 
	//                    Leave empty if you want to overwrite your original. 
	//    ret:   none
	//    about: Finds basic info about the original image
	// ------------------------------------------------------------------------
	public function PhotoManip($originalFileName, $finalFileName = 'overwrite')
	{
		$this->originalFileName = $originalFileName;
		$this->finalFileName    = $finalFileName;
		$this->fileType         = exif_imagetype($originalFileName);
		list($this->originalWidth, $this->originalHeight) = getimagesize($originalFileName);
		$this->createOriginalResource();
	}
	
	// ========================================================================
	// thumbnail
	//    args:  int - final width of the thumbnail
	//           int - final height of the thumbnail
	//    ret:   none
	//    about: Creates a thumbnail from largest possible box from the 
	//           original matching the given aspect ratio.  Don't worry if the 
	//           thumbnail is actually larger than the original - this method 
	//           correctly handles stretching.
	// ------------------------------------------------------------------------
	public function thumbnail($finalWidth, $finalHeight)
	{
		$largestWidth  = $this->originalHeight / $finalHeight * $finalWidth;
		$largestHeight = $this->originalWidth  / $finalWidth  * $finalHeight;
	
		// sides must be cropped
		if($largestWidth < $this->originalWidth)
			$this->crop(floor($this->originalWidth / 2) - $largestWidth / 2, 0, $largestWidth, $this->originalHeight, $finalWidth, $finalHeight);
			
		// top and bottom must be cropped
		else
			$this->crop(0, floor($this->originalHeight / 2) - $largestHeight / 2, $this->originalWidth, $largestHeight, $finalWidth, $finalHeight);
	}
	
	// ========================================================================
	// crop
	//    args:  int - width on the original starting at startX that you want
	//                 to compress/enlarge into the final image.
	//           int - height on the original starting at startY that you want
	//                 to compress/enlarge into the final image.
	//           int - final width of the thumbnail
	//           int - final height of the thumbnail
	//    ret:   none
	//    about: Just like the Photoshop crop, this can do a plain chopping:
	//           (set $finalWidth = $origWidth and $finalHeight = $origHeight)
	//           or it can do a chop + resize too:
	//           (set $orig[...] independently from $final[...])
	// ------------------------------------------------------------------------
	public function crop($startX, $startY, $cropWidth, $cropHeight, $finalWidth, $finalHeight)
	{
		$this->finalResource = imagecreatetruecolor($finalWidth, $finalHeight);
		
		imagecopyresampled($this->finalResource, $this->originalResource, 0, 0, 
			$startX, $startY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);
		
		$this->createFinalImage();
	}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// PRIVATE METHODS
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	// ========================================================================
	// createOriginalResource
	//    args:  none
	//    ret:   void
	//    about: The image must be loaded into a special resource for PHP to
	//           do any kind of manipulation to it.
	// ------------------------------------------------------------------------
	private function createOriginalResource()
	{	
		switch($this->fileType)
		{	
			case IMAGETYPE_GIF:  $this->originalResource = imagecreatefromgif($this->originalFileName);  break;
			case IMAGETYPE_PNG:  $this->originalResource = imagecreatefrompng($this->originalFileName);  break;
			case IMAGETYPE_BMP:  $this->originalResource = imagecreatefromwbmp($this->originalFileName); break;
			case IMAGETYPE_JPEG: $this->originalResource = imagecreatefromjpeg($this->originalFileName); break;
			default: die('PhotoManip->createOriginalResource(): file was not an image');
		}
	}
	
	// ========================================================================
	// createFinalImage
	//    args:  none
	//    ret:   void
	//    about: Saves the final image file to disk.  Will handle deleteing of
	//           original if overwriting was desired. 
	//    TODO:  we support importing of any file type, but exporting of only
	//           JPEG.
	// ------------------------------------------------------------------------
	private function createFinalImage()
	{	

		if($this->finalFileName != 'overwrite')
		{
			imagejpeg($this->finalResource, $this->finalFileName);
		}
		else
		{
			unlink($this->originalFileName);
			imagejpeg($this->finalResource, $this->originalFileName);
		}
		
		// free up RAM
		imagedestroy($this->originalResource);
		imagedestroy($this->finalResource); 
	}
}

?>