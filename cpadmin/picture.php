<?php

$catID = $_REQUEST["catID"];
$catDir = $_REQUEST["catDir"];
$imgName = $_REQUEST["pic"];
$imgUser = $_REQUEST["u"];

$mw = $_REQUEST["w"];

$cache_path = "images/cache/";

$path=urldecode(str_replace("*DD","%2F",$_REQUEST["n"]));

if(substr($path,0,1)=="/")
	$path = substr($path,1,200);
	
$imgRoot = "image";
if($imgUser=="1")
	$imgRoot = "usernews";

if (!isset($_GET['n']))
{
	header('HTTP/1.1 400 Bad Request');
	echo 'Error: no image was specified';
	exit();
}

define('MEMORY_TO_ALLOCATE',	'100M');
define('DEFAULT_QUALITY',		90);
define('CURRENT_DIR',			dirname(__FILE__));
define('CACHE_DIR_NAME',		'/images/cache/');
define('CACHE_DIR',				CURRENT_DIR . CACHE_DIR_NAME);
define('DOCUMENT_ROOT',			$_SERVER['DOCUMENT_ROOT']."/postpic/$imgRoot/");

$image = $path ;

// For security, directories cannot contain ':', images cannot contain '..' or '<', and
// images must start with '/'
if (strpos(dirname($image), ':') || preg_match('/(\.\.|<|>)/', $image))
{
	header('HTTP/1.1 400 Bad Request');
	echo 'Error: malformed image path. Image paths must begin with \'/\'';
	exit();
}

// If the image doesn't exist, or we haven't been told what it is, there's nothing
// that we can do
if (!$image)
{
	header('HTTP/1.1 400 Bad Request');
	echo 'Error: no image was specified';
	exit();
}

// Strip the possible trailing slash off the document root
$docRoot	= preg_replace('/\/$/', '', DOCUMENT_ROOT);
$docRoot.="/";
if (!file_exists($docRoot . $image))
{
	header('HTTP/1.1 404 Not Found');
	echo 'Error: image does not exist: ';
	exit();
}

// Get the size and MIME type of the requested image
$size	= GetImageSize($docRoot . $image);
$mime	= $size['mime'];

// Make sure that the requested file is actually an image
if (substr($mime, 0, 6) != 'image/')
{
	header('HTTP/1.1 400 Bad Request');
	echo 'Error: requested file is not an accepted type: ';
	exit();
}

$width			= $size[0];
$height			= $size[1];

$maxWidth		= (isset($_GET['w'])) ? (int) $_GET['w'] : 0;
$maxHeight		= (isset($_GET['h'])) ? (int) $_GET['h'] : 0;


if($_REQUEST["minWid"]>0 and $_REQUEST["orWid"]>0){
	if($size[0] > $_REQUEST["minWid"] ){
		$maxWidth = $_REQUEST["minWid"];
	}
	else
		$maxWidth = $_REQUEST["orWid"];
}

if($_REQUEST["mh"]>0){
	$mh1 = $_REQUEST["mh"];
    $h11 = $size[1];
    $w11 =  $size[0];
     if ($h11 > $mh1)
	 {
		   $h11 = $mh1;
		   $percent = ($size[1] / $h11);
		   $w11 = ($size[0] / $percent);
	 }
	
	$maxWidth =$w11;
}


if (isset($_GET['color']))
	$color		= preg_replace('/[^0-9a-fA-F]/', '', (string) $_GET['color']);
else
	$color		= FALSE;

// If either a max width or max height are not specified, we default to something
// large so the unspecified dimension isn't a constraint on our resized image.
// If neither are specified but the color is, we aren't going to be resizing at
// all, just coloring.
if (!$maxWidth && $maxHeight)
{
	$maxWidth	= 99999999999999;
}
elseif ($maxWidth && !$maxHeight)
{
	$maxHeight	= 99999999999999;
}
elseif ($color && !$maxWidth && !$maxHeight)
{
	$maxWidth	= $width;
	$maxHeight	= $height;
}

// If we don't have a max width or max height, OR the image is smaller than both
// we do not want to resize it, so we simply output the original image and exit
if ((!$maxWidth && !$maxHeight) || (!$color && $maxWidth >= $width && $maxHeight >= $height))
{
	$data	= file_get_contents($docRoot . '/' . $image);
	
	$lastModifiedString	= gmdate('D, d M Y H:i:s', filemtime($docRoot . '/' . $image)) . ' GMT';
	$etag				= md5($data);
	
	doConditionalGet($etag, $lastModifiedString);
	
	header("Content-type: $mime");
	header('Content-Length: ' . strlen($data));
	echo $data;
	exit();
}

// Ratio cropping
$offsetX	= 0;
$offsetY	= 0;

if (isset($_GET['cropratio']))
{
	$cropRatio		= explode(':', (string) $_GET['cropratio']);
	if (count($cropRatio) == 2)
	{
		$ratioComputed		= $width / $height;
		$cropRatioComputed	= (float) $cropRatio[0] / (float) $cropRatio[1];
		
		if ($ratioComputed < $cropRatioComputed)
		{ // Image is too tall so we will crop the top and bottom
			$origHeight	= $height;
			$height		= $width / $cropRatioComputed;
			$offsetY	= ($origHeight - $height) / 2;
		}
		else if ($ratioComputed > $cropRatioComputed)
		{ // Image is too wide so we will crop off the left and right sides
			$origWidth	= $width;
			$width		= $height * $cropRatioComputed;
			$offsetX	= ($origWidth - $width) / 2;
		}
	}
}

// Setting up the ratios needed for resizing. We will compare these below to determine how to
// resize the image (based on height or based on width)
$xRatio		= $maxWidth / $width;
$yRatio		= $maxHeight / $height;

if ($xRatio * $height < $maxHeight)
{ // Resize the image based on width
	$tnHeight	= ceil($xRatio * $height);
	$tnWidth	= $maxWidth;
}
else // Resize the image based on height
{
	$tnWidth	= ceil($yRatio * $width);
 	$tnHeight	= $maxHeight;
}

// Determine the quality of the output image
$quality	= (isset($_GET['quality'])) ? (int) $_GET['quality'] : DEFAULT_QUALITY;

// Before we actually do any crazy resizing of the image, we want to make sure that we
// haven't already done this one at these dimensions. To the cache!
// Note, cache must be world-readable

// We store our cached image filenames as a hash of the dimensions and the original filename
$resizedImageSource		= $tnWidth . 'x' . $tnHeight . 'x' . $quality;
if ($color)
	$resizedImageSource	.= 'x' . $color;
if (isset($_GET['cropratio']))
	$resizedImageSource	.= 'x' . (string) $_GET['cropratio'];
$resizedImageSource		.= '-' . $image;

$resizedImage	= md5($resizedImageSource);
	
$resized		= CACHE_DIR . $resizedImage;
$_GET['nocache']="ok";
// Check the modified times of the cached file and the original file.
// If the original file is older than the cached file, then we simply serve up the cached file
if (!isset($_GET['nocache']) && file_exists($resized))
{
	$imageModified	= filemtime($docRoot . $image);
	$thumbModified	= filemtime($resized);
	
	if($imageModified < $thumbModified) {
		$data	= file_get_contents($resized);
	
		$lastModifiedString	= gmdate('D, d M Y H:i:s', $thumbModified) . ' GMT';
		$etag				= md5($data);
		
		doConditionalGet($etag, $lastModifiedString);
		
		header("Content-type: $mime");
		header('Content-Length: ' . strlen($data));
		echo $data;
		exit();
	}
}

// We don't want to run out of memory
ini_set('memory_limit', MEMORY_TO_ALLOCATE);

// Set up a blank canvas for our resized image (destination)
$dst	= imagecreatetruecolor($tnWidth, $tnHeight);

// Set up the appropriate image handling functions based on the original image's mime type
switch ($size['mime'])
{
	case 'image/gif':
		// We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
		// This is maybe not the ideal solution, but IE6 can suck it
		$creationFunction	= 'ImageCreateFromGif';
		$outputFunction		= 'ImagePng';
		$mime				= 'image/png'; // We need to convert GIFs to PNGs
		$doSharpen			= FALSE;
		$quality			= round(10 - ($quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
	break;
	
	case 'image/x-png':
	case 'image/png':
		$creationFunction	= 'ImageCreateFromPng';
		$outputFunction		= 'ImagePng';
		$doSharpen			= FALSE;
		$quality			= round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
	break;
	
	default:
		$creationFunction	= 'ImageCreateFromJpeg';
		$outputFunction	 	= 'ImageJpeg';
		$doSharpen			= FALSE;
	break;
}

// Read in the original image
$src	= $creationFunction($docRoot . $image);

$x = imagesx($src);
$y = imagesy($src);
for ($i = 0; $i < $x; $i++) {
  for ($j = 0; $j < $y; $j++) {
    $rgb = imagecolorat($src, $i, $j);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
     //for gray mode $r = $g = $b
    $gg = round(($r + $g + $b) / 3);
               
                // grayscale values have r=g=b=g
   
	$val = imagecolorallocate($src, $gg, $gg, $gg);
   
	// set the gray value
   
	imagesetpixel ($src, $i, $j, $val);
   }
}

if (in_array($size['mime'], array('image/gif', 'image/png')))
{
	if (!$color)
	{
		// If this is a GIF or a PNG, we need to set up transparency
		imagealphablending($dst, false);
		imagesavealpha($dst, true);
	}
	else
	{
		// Fill the background with the specified color for matting purposes
		if ($color[0] == '#')
			$color = substr($color, 1);
		
		$background	= FALSE;
		
		if (strlen($color) == 6)
			$background	= imagecolorallocate($dst, hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));
		else if (strlen($color) == 3)
			$background	= imagecolorallocate($dst, hexdec($color[0].$color[0]), hexdec($color[1].$color[1]), hexdec($color[2].$color[2]));
		if ($background)
			imagefill($dst, 0, 0, $background);
	}
}

// Resample the original image into the resized canvas we set up earlier
ImageCopyResampled($dst, $src, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $width, $height);

$waterMark = true;
if ($tnWidth>200) {
	
    $insertDst = imagecreatefrompng("watermark.png");

    $insert_x = imagesx($insertDst);
    $insert_y = imagesy($insertDst);

    $dest_x = imagesx($dst) - $insert_x - 10;
    $dest_y = imagesy($dst) - $insert_y - 10;

    imagecopy($dst, $insertDst, $dest_x, $dest_y, 0, 0, $insert_x, $insert_y);
} 


if ($doSharpen)
{
	// Sharpen the image based on two things:
	//	(1) the difference between the original size and the final size
	//	(2) the final size
	$sharpness	= findSharp($width, $tnWidth);
	
	$sharpenMatrix	= array(
		array(-1, -2, -1),
		array(-2, $sharpness + 12, -2),
		array(-1, -2, -1)
	);
	$divisor		= $sharpness;
	$offset			= 0;
	imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
}

// Make sure the cache exists. If it doesn't, then create it
if (!file_exists(CACHE_DIR))
	mkdir(CACHE_DIR, 0755);

// Make sure we can read and write the cache directory
if (!is_readable(CACHE_DIR))
{
	header('HTTP/1.1 500 Internal Server Error');
	echo 'Error: the cache directory is not readable';
	exit();
}
else if (!is_writable(CACHE_DIR))
{
	header('HTTP/1.1 500 Internal Server Error');
	echo 'Error: the cache directory is not writable';
	exit();
}

// Write the resized image to the cache
$outputFunction($dst, $resized, $quality);

// Put the data of the resized image into a variable
ob_start();
$outputFunction($dst, null, $quality);
$data	= ob_get_contents();
ob_end_clean();

// Clean up the memory
ImageDestroy($src);
ImageDestroy($dst);

// See if the browser already has the image
$lastModifiedString	= gmdate('D, d M Y H:i:s', filemtime($resized)) . ' GMT';
$etag				= md5($data);

doConditionalGet($etag, $lastModifiedString);

// Send the image to the browser with some delicious headers
header("Content-type: $mime");
header('Content-Length: ' . strlen($data));
echo $data;

function findSharp($orig, $final) // function from Ryan Rud (http://adryrun.com)
{
	$final	= $final * (750.0 / $orig);
	$a		= 52;
	$b		= -0.27810650887573124;
	$c		= .00047337278106508946;
	
	$result = $a + $b * $final + $c * $final * $final;
	
	return max(round($result), 0);
} // findSharp()

function doConditionalGet($etag, $lastModified)
{
	header("Last-Modified: $lastModified");
	header("ETag: \"{$etag}\"");
		
	$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
		stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : 
		false;
	
	$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
		stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
		false;
	
	if (!$if_modified_since && !$if_none_match)
		return;
	
	if ($if_none_match && $if_none_match != $etag && $if_none_match != '"' . $etag . '"')
		return; // etag is there but doesn't match
	
	if ($if_modified_since && $if_modified_since != $lastModified)
		return; // if-modified-since is there but doesn't match
	
	// Nothing has changed since their last request - serve a 304 and exit
	header('HTTP/1.1 304 Not Modified');
	exit();
} // doConditionalGet()

// old pond
// a frog jumps
// the sound of water

// —Matsuo Basho
?>