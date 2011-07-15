<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

include_once(realpath(dirname(__FILE__).'/../compat/gd_info.php'));

/* exdoc
 * The definition of this constant lets other parts of the subsystem know
 * that the Image Subsystem has been included for use.
 * @node Subsystems:Image
 */
define('SYS_IMAGE',1);

define('IMAGE_ERR_NOGD','');
define('IMAGE_ERR_NOTSUPPORTED','_unknown');
define('IMAGE_ERR_FILENOTFOUND','_notfound');
define('IMAGE_ERR_PERMISSIONDENIED','_denied');

/* exdoc
 * Output the contents of the fallback preview image.
 *
 * This is used by the dynamic thumbnail script (/thumb.php)
 * if it finds that the server does not have to appropriate GD
 * support to generate a specific type of preview (i.e. no GIF support)
 * or if GD isn't enabled at all.
 *
 * @param string $base The base directory of Exponent.  Refer to the BASE constant.
 * @node Subsystems:Image
 */
function exponent_image_showFallbackPreviewImage($base,$error = IMAGE_ERR_NOGD) {
	$fh = fopen($base.'subsystems/image/default_preview'.$error.'.gif','rb');
	$img = fread($fh,65536);
	fclose($fh);
	header('Content-type: image/gif');
	echo $img;
}

/* exdoc
 * Return size and mimetype information about an image file,
 * given its path/filename.  This is a wrapper around various
 * GD functions, to make all implementations work identically.
 *
 * @param string $filename The path to the file to query.
 * @node Subsystems:Image
 */
function exponent_image_sizeinfo($filename) {
	if (!file_exists($filename)) {
		return IMAGE_ERR_FILENOTFOUND;
	}
	if (!is_readable($filename)) {
		return IMAGE_ERR_PERMISSIONDENIED;
	}
	$sizeinfo = @getimagesize($filename);
	if (!isset($sizeinfo['mime'])) {
		// In case this implementation of getimagesize
		$types = array(
			'jpg'=>'image/jpeg',
			'jpeg'=>'image/jpeg',
			'gif'=>'image/gif',
			'png'=>'image/png'
		);
		$lowerfile = strtolower($filename);
		foreach ($types as $type=>$mime) {
			if (substr($lowerfile,-1*strlen($type),strlen($type)) == $type) $sizeinfo['mime'] = $mime;
		}
	}

	return $sizeinfo;
}

/* exdoc
 * Create an image resource handle (from GD) for a given filename.
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * At this point, the user should have called exponent_image_sizeInfo on the filename
 * and verified that the file does indeed exist, and is readable.  A safeguard check
 * is in place, however.
 *
 * @param string $filename The path/filename of the image.
 * @param Array $sizeinfo Size information (as returned by exponent_image_sizeInfo
 * @node Subsystems:Image
 */
function exponent_image_createFromFile($filename,$sizeinfo) {
	if (!EXPONENT_HAS_GD) {
		return null;
	}

	$info = gd_info();

	if ($sizeinfo['mime'] == 'image/jpeg' && $info['JPG Support'] == true) {
		$img = imagecreatefromjpeg($filename);
	} else if ($sizeinfo['mime'] == 'image/png' && $info['PNG Support'] == true) {
		$img = imagecreatefrompng($filename);
	} else if ($sizeinfo['mime'] == 'image/gif' && $info['GIF Read Support'] == true) {
		$img = imagecreatefromgif($filename);
	} else {
		// Either we have an unknown image type, or an unsupported image type.
		return IMAGE_ERR_NOTSUPPORTED;
	}

	if (function_exists('imagesavealpha')) {
		imagealphablending($img, false);
		imagesavealpha($img, true);
	}
	return $img;
}

/* exdoc
 * Create a new blank image resource, with the specified width and height.
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * @param integer $w Width of the image resource to create (in pixels)
 * @param integer $h Height of the image resource to create (in pixels)
 * @node Subsystems:Image
 */
function exponent_image_create($w,$h) {
	if (!EXPONENT_HAS_GD) {
		return null;
	}
	$info = gd_info();
	if (strpos($info['GD Version'],'2.0') !== false) {
		$img = imagecreatetruecolor($w,$h);

		if (function_exists('imagesavealpha')) {
			imagealphablending($img, false);
			imagesavealpha($img, true);
		}

		return $img;
	} else {
		return imagecreate($w,$h);
	}
}

function exponent_image_copyresized($dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
	if (!EXPONENT_HAS_GD) {
		return null;
	}
	$info = gd_info();
	if (strpos($info['GD Version'],'2.0') !== false) {
		return imagecopyresampled($dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	} else {
		return imagecopyresized($dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	}
}

/* exdoc
 * Proportionally scale an image by a specific percentage
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * @param string $filename The path/filename of the image to scale.
 * @param decimal $scale The scaling factor, as a decimal (i.e. 0.5 for 50%)
 * @node Subsystems:Image
 */
function exponent_image_scaleByPercent($filename,$scale) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}

	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $original;
	}

	if ($scale == 1) {
		return $original;
	}

	$w = $scale * $sizeinfo[0];
	$h = $scale * $sizeinfo[1];

	$thumb = exponent_image_create($w,$h);
	if (!$thumb) return null;
	exponent_image_copyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

	return $thumb;
}

/* exdoc
 * Proportionally scale an image to a given width. Height adjusts accordingly.
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * @param string $filename The path/filename of the image to scale.
 * @param integer $width The desired width of the scaled image, in pixels.
 * @node Subsystems:Image
 */
function exponent_image_scaleToWidth($filename,$width) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}
	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $sizeinfo;
	}

	if ($width == $sizeinfo[0]) {
		return $original;
	}

	$w = $width;
	$h = ($width / $sizeinfo[0]) * $sizeinfo[1];

	$thumb = exponent_image_create($w,$h);
	if (!$thumb) return null;
	exponent_image_copyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

	return $thumb;
}

/* exdoc
 * Proportionally scale an image to a given height.  Width adjusts accordingly.
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * @param string $filename The path/filename of the image to scale.
 * @param integer $height The desired height of the scaled image, in pixels.
 * @node Subsystems:Image
 */function exponent_image_scaleToHeight($filename,$height) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}

	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $original;
	}

	if ($height == $sizeinfo[1]) {
		return $original;
	}

	$w = ($height / $sizeinfo[1]) * $sizeinfo[0];
	$h = $height;

	$thumb = exponent_image_create($w,$h);
	if (!$thumb) return null;
	exponent_image_copyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

	return $thumb;
}

/* exdoc
 * Proportionally scale an image to fit within the given width / height.
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * @param string $filename The path/filename of the image to scale.
 * @param integer $width The maximum width of the scaled image, in pixels.
 * @param integer $height The maximum height of the scaled image, in pixels.
 * @node Subsystems:Image
 */
function exponent_image_scaleToConstraint($filename,$width,$height) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}

	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $original;
	}

	if ($width >= $sizeinfo[0] && $height >= $sizeinfo[1]) {
		return $original;
	}

	$w = $width;
	$h = ($width / $sizeinfo[0]) * $sizeinfo[1];

	if ($h > $height) { // height is outside
		$w = ($height / $sizeinfo[1]) * $sizeinfo[0];
		$h = $height;
	}

	$thumb = exponent_image_create($w,$h);
	if (!$thumb) return null;
	exponent_image_copyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

	return $thumb;
}
/* exdoc
 * Scale an image to a square keeping the image aspect ratio.
 * If the image is smaller in either dimension than request square side original is returned.
 * Image is first cropped to a square of length smaller of width or height and then resized.
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * @param string $filename The path/filename of the image to scale.
 * @param integer $size The desired side length of the scaled image, in pixels.
 * @node Subsystems:Image
 */
function exponent_image_scaleToSquare($filename,$side) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}

	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $original;
	}

	if ($side >= $sizeinfo[0] || $side >= $sizeinfo[1]) {
		return $original;
	}

	/* The defaults will serve in case the image is a square */
	$src_x = 0;
	$src_y = 0;
	$width = $sizeinfo[0];
	$height = $sizeinfo[1];

	/*if width greater than height, we crop the image left and right */
	if ($sizeinfo[0] > $sizeinfo[1]) {
		$width=$sizeinfo[1];
		$height=$sizeinfo[1];
		$src_x=round(($sizeinfo[0]-$width)/2,0);
	}
	else
	{
	/*if height greater than width, we crop the image top and bottom */
		$height=$sizeinfo[0];
		$width=$sizeinfo[0];
		$src_y=round(($sizeinfo[1]-$height)/2,0);
	}

	$w = $side;
	$h = $side;

	$thumb = exponent_image_create($w,$h);
	if (!$thumb) return null;
   exponent_image_copyresized($thumb,$original,0,0,$src_x,$src_y,$w,$h,$width,$height);

	return $thumb;
}

/* exdoc
 * Scale an image to a given width and height, without regard to aspect ratio.
 * This is a wrapper around various GD functions, to provide Exponent
 * programmers a single point of entry.  It also handles situations where
 * there is no GD support compiled into the server.  (In this case, null is returned).
 *
 * @param string $filename The path/filename of the image to scale.
 * @param integer $width The desired width of the scaled image, in pixels.
 * @param integer $height The desired height of the scaled image, in pixels.
 * @node Subsystems:Image
 */
function exponent_image_scaleManually($filename,$width,$height) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}

	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $original;
	}

	if ($width == $sizeinfo[0] && $height == $sizeinfo[1]) {
		return $original;
	}

	$thumb = exponent_image_create($width,$height);
	if (!$thumb) return null;
	exponent_image_copyresized($thumb,$original,0,0,0,0,$width,$height,$sizeinfo[0],$sizeinfo[1]);

	return $thumb;
}

function exponent_image_rotate($filename,$degrees) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}

	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $original;
	}

	$color = imagecolorclosesthwb($original,255,255,255);

	return imagerotate($original,$degrees,$color);
}

function exponent_image_flip($filename,$is_horizontal) {
	$sizeinfo = exponent_image_sizeinfo($filename);
	if (!is_array($sizeinfo)) {
		return $sizeinfo;
	}

	$original = exponent_image_createFromFile($filename,$sizeinfo);
	if (!is_resource($original)) {
		return $original;
	}

	// Horizontal - invert y coords
	// Vertical - invert x coords

	$w = $sizeinfo[0];
	$h = $sizeinfo[1];
	$new = exponent_image_create($w,$h);

	if ($is_horizontal) {
		// Copy column by column
		//$dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
		for ($i = 0; $i < $w; $i++) {
			imagecopy($new,$original, // DESTINATION, SOURCE
			$i,0,		// dst_X, dst_Y
			$w-$i-1,0,	// src_X,src_Y
			1,$h);		//src_W, src_H
		}
	} else {
		// Copy row by row.
		//$dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
		for ($i = 0; $i < $h; $i++) {
			imagecopy($new,$original, // DESTINATION, SOURCE
			0,$i,		// dst_X, dst_Y
			0,$h-$i-1,	// src_X,src_Y
			#$w,1,		// dst_W, dst_H
			$w,1);		//src_W, src_H
		}
	}
	return $new;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_image_output($img, $sizeinfo, $filename=null,  $quality=75) {
	header('Content-type: ' . $sizeinfo['mime']);
	if ($sizeinfo['mime'] == 'image/jpeg') {
		($filename != null) ? imagejpeg($img, $filename, $quality) : imagejpeg($img, null, $quality);
	} else if ($sizeinfo['mime'] == 'image/png') {
		($filename != null) ? imagepng($img,$filename) : imagepng($img);
	} else if ($sizeinfo['mime'] == 'image/gif') {
		($filename != null) ? imagepng($img,$filename) : imagepng($img);
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_image_captcha($w,$h,$string) {
	$img = exponent_image_create($w,$h);
	if ($img) {
		// We were able to create an image.
		$bg = 		imagecolorallocate($img,250,255,225);
		imagefill($img,0,0,$bg);
		#echo $bg;
		$colors = array();
		for ($i = 0; $i < strlen($string) && $i < 10; $i++) {
			$colors[$i] = imagecolorallocate($img,rand(50,150),rand(50,150),rand(50,150));
		}
		$px_per_char = floor($w / (strlen($string)+1));
		for ($i = 0; $i < strlen($string); $i++) {
			imagestring($img,rand(4,6),$px_per_char * ($i+1) + rand(-5,5),rand(0,$h / 2),$string{$i},$colors[($i % 10)]);
		}

		// Need this to be 'configurable'
		for ($i = 0; $i < strlen($string) / 2 && $i < 10; $i++) {
			$c = imagecolorallocate($img,rand(150,250),rand(150,250),rand(150,250));
			imageline($img,rand(0,$w / 4),rand(5, $h-5),rand(3*$w / 4,$w),rand(0,$h),$c);
		}

		//imagestring($img,6,0,0,$string,$color);
		return $img;
	} else {
		return null;
	}
}

?>