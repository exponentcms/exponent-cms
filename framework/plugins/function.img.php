<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {img} function plugin
 *
 * Type:     function<br>
 * Name:     img<br>
 * Purpose:  display an image using phpthumb-nailer
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return string
 */
function smarty_function_img($params,&$smarty) {
	$closing = (XHTML==1) ? ' />' : '>';
    $id = (isset($params['id'])) ? ' id="'.$params['id'].'"' : '';
    $alt = (isset($params['alt'])) ? ' alt="'.$params['alt'].'"' : '';
    $title = (isset($params['title'])) ? ' title="'.$params['title'].'"' : '';
    $class = (isset($params['class'])) ? ' class="'.$params['class'].'"' : '';
    $style = (isset($params['style'])) ? ' style="'.$params['style'].'"' : '';
    $rel = (isset($params['rel'])) ? ' rel="'.$params['rel'].'"' : '';
    $dims = (isset($params['w']) && isset($params['h']) && isset($params['zc'])) ? ' height="'.$params['h'].'" width="'.$params['w'].'"' : '';
        
	if (!isset($params['q']) && defined('THUMB_QUALITY')) $params['q'] = THUMB_QUALITY;

	$src = PATH_RELATIVE.'thumb.php?';
	
	// figure out which file we're showing
	//if (isset($params['src'])) $src .= '?src='.$params['src'];
	if (isset($params['file'])) $src .= 'file='.$params['file'];
	if (isset($params['file_id'])) $src .= 'id='.$params['file_id'];
    
	/*if (((isset($params['width']) || isset($params['height']) || isset($params['square']))&&empty($params['lgcy']))||(!empty($params['lgcy']))) {
	    $src .= '&amp;lgcy=1';
	    
		// get the image dimensions
		if (isset($params['constrain'])) $src .= '&amp;constraint=1'; 
		if (isset($params['square'])) $src .= '&amp;square='.$params['square'];
		if (isset($params['width'])) $src .= '&amp;width='.$params['width'];
		if (isset($params['height'])) $src .= '&amp;height='.$params['height'];
		    	
	} else { */
	    //  phpthumb get angry unless you pass it certain values:
	    $allowedGETparameters = array(  //full explanation available here: http://phpthumb.sourceforge.net/demo/docs/phpthumb.readme.txt
								'src', 	// source
		 						'new',  // create new image, not thumb of existing
		 						'w',	// width
		 						'h', 	// height
		  						'wp',	// max width for portrait images
		   						'hp',	// max height for portrait images
		   						'wl', 	// max width for landscape images
								'hl', 	// max height for landscape images
								'ws', 	// max width for square images
								'hs', 	// max height for square images
								'f', 	// output image format (jpeg, gif, png)
								'q', 	// JPEG compression (1=worst, 95=best, 75=default)
								'sx', 	// left side of source rectangle (default = 0) (values 0 < sx < 1 represent percentage)
								'sy', 	// top side of source rectangle (default = 0) (values 0 < sy < 1 represent percentage)
								'sw', 	// width of source rectangle (default = fullwidth) (values 0 < sw < 1 represent percentage)
								'sh', 	// height of source rectangle (default = fullheight) (values 0 < sh < 1 represent percentage)
								'zc', 	/* zoom-crop. Will auto-crop off the larger dimension
									        so that the image will fill the smaller dimension
       										(requires both "w" and "h", overrides "iar", "far")
       										Set to "1" or "C" to zoom-crop towards the center,
      										or set to "T", "B", "L", "R", "TL", "TR", "BL", "BR"
       										to gravitate towards top/left/bottom/right directions
       										(requires ImageMagick for values other than "C" or "1")
										*/
								'bc', 	// border hex color (default = 000000)
								'bg', 	// background hex color (default = FFFFFF)
								'bgt', 	// 
								'fltr', /*filter system. Call as an array as follows:
										   - "brit" (Brightness) [ex: &fltr[]=brit|<value>]
											 where <value> is the amount +/- to adjust brightness
											 (range -255 to 255)
											 Available in PHP5 with bundled GD only.
										   - "cont" (Contrast) [ex: &fltr[]=cont|<value>]
											 where <value> is the amount +/- to adjust contrast
											 (range -255 to 255)
											 Available in PHP5 with bundled GD only.
										   - "gam" (Gamma Correction) [ex: &fltr[]=gam|<value>]
											 where <value> can be a number >0 to 10+ (default 1.0)
											 Must be >0 (zero gives no effect). There is no max,
											 although beyond 10 is pretty useless. Negative
											 numbers actually do something, maybe not quite the
											 desired effect, but interesting nonetheless.
										   - "sat" (SATuration) [ex: &fltr[]=sat|<value>]
											 where <value> is a number between zero (no change)
											 and -100 (complete desaturation = grayscale), or it
											 can be any positive number for increased saturation.
										   - "ds" (DeSaturate) [ex: &fltr[]=ds|<value>]
											 is an alias for "sat" except values are inverted
											 (positive values remove color, negative values boost
											 saturation)
										   - "gray" (Grayscale) [ex: &fltr[]=gray]
											 remove all color from image, make it grayscale
										   - "th" (Threshold) [ex: &fltr[]=th|<value>]
											 makes image greyscale, then sets all pixels brighter
											 than <value> (range 0-255) to white, and all pixels
											 darker than <value> to black
										   - "rcd" (Reduce Color Depth) [ex: &fltr[]=rcd|<c>|<d>]
											 where <c> is the number of colors (2-256) you want
											 in the output image, and <d> is "1" for dithering
											 (default) or "0" for no dithering
										   - "clr" (Colorize) [ex: &fltr[]=clr|<value>|<color>]
											 where <value> is a number between 0 and 100 for the
											 amount of colorization, and <color> is the hex color
											 to colorize to.
										   - "sep" (Sepia) [ex: &fltr[]=sep|<value>|<color>]
											 where <value> is a number between 0 and 100 for the
											 amount of colorization (default=50), and <color> is
											 the hex color to colorize to (default=A28065).
											 Note: this behaves differently when applied by
											 ImageMagick, in which case 80 is default, and lower
											 values give brighter/yellower images and higher
											 values give darker/bluer images
										   - "usm" (UnSharpMask) [ex: &fltr[]=usm|<a>|<r>|<t>]
											 where <a> is the amount (default = 80), <r> is the
											 radius (default = 0.5), <t> is the threshold
											 (default = 3).
										   - "blur" (Blur) [ex: &fltr[]=blur|<radius>]
											 where (0 < <radius> < 25) (default = 1)
										   - "gblr" (Gaussian Blur) [ex: &fltr[]=gblr]
											 Available in PHP5 with bundled GD only.
										   - "sblr" (Selective Blur) [ex: &fltr[]=gblr]
											 Available in PHP5 with bundled GD only.
										   - "smth" (Smooth) [ex: &fltr[]=smth|<value>]
											 where <value> is the weighting value for the matrix
											 (range -10 to 10, default 6)
											 Available in PHP5 with bundled GD only.
										   - "lvl" (Levels)
											 [ex: &fltr[]=lvl|<channel>|<method>|<threshold>
											 where <channel> can be one of 'r', 'g', 'b', 'a' (for
											 Red, Green, Blue, Alpha respectively), or '*' for all
											 RGB channels (default) based on grayscale average.
											 ImageMagick methods can support multiple channels
											 (eg "lvl|rg|3") but internal methods cannot (they will
											 use first character of channel string as channel)
											 <method> can be one of:
											 0=Internal RGB;
											 1=Internal Grayscale;
											 2=ImageMagick Contrast-Stretch (default)
											 3=ImageMagick Normalize (may appear over-saturated)
											 <threshold> is how much of brightest/darkest pixels
											 will be clipped in percent (default = 0.1%)
											 Using default parameters (&fltr[]=lvl) is similar to
											 Auto Contrast in Adobe Photoshop.
										   - "wb" (White Balance) [ex: &fltr[]=wb|<c>]
											 where <c> is the target hex color to white balance
											 on, this color is what "should be" white, or light
											 gray. The filter attempts to maintain brightness so
											 any gray color can theoretically be used. If <c> is
											 omitted the filter guesses based on brightest pixels
											 in each of RGB
											 OR <c> can be the percent of white clipping used
											 to calculate auto-white-balance (default = 0.1%)
											 NOTE: "wb" in default settings already gives an effect
											 similar to "lvl", there is usually no need to use "lvl"
											 if "wb" is already used.
										   - "hist" (Histogram)
											 [ex: &fltr[]=hist|<b>|<c>|<w>|<h>|<a>|<o>|<x>|<y>]
											 Where <b> is the color band(s) to display, from back
											 to front (one or more of "rgba*" for Red Green Blue
											 Alpha and Grayscale respectively);
											 <c> is a semicolon-seperated list of hex colors to
											 use for each graph band (defaults to FF0000, 00FF00,
											 0000FF, 999999, FFFFFF respectively);
											 <w> and <h> are the width and height of the overlaid
											 histogram in pixels, or if <= 1 then percentage of
											 source image width/height;
											 <a> is the alignment (same as for "wmi" and "wmt");
											 <o> is opacity from 0 (transparent) to 100 (opaque)
												 (requires PHP v4.3.2, otherwise 100% opaque);
											 <x> and <y> are the edge margin in pixels (or percent
												 if 0 < (x|y) < 1)
										   - "over" (OVERlay/underlay image) overlays an image on
											 the thumbnail, or overlays the thumbnail on another
											 image (to create a picture frame for example)
											 [ex: &fltr[]=over|<i>|<u>|<m>|<o>]
											 where <i> is the image filename; <u> is "0" (default)
											 for overlay the image on top of the thumbnail or "1"
											 for overlay the thumbnail on top of the image; <m> is
											 the margin - can be absolute pixels, or if < 1 is a
											 percentage of the thumbnail size [must be < 0.5]
											 (default is 0 for overlay and 10% for underlay);
											 <o> is opacity (0 = transparent, 100 = opaque)
												 (requires PHP v4.3.2, otherwise 100% opaque);
											 (thanks raynerape?gmail*com, shabazz3?msu*edu)
										   - "wmi" (WaterMarkImage)
											 [ex: &fltr[]=wmi|<f>|<a>|<o>|<x>|<y>|<r>] where
											 <f> is the filename of the image to overlay;
											 <a> is the alignment (one of BR, BL, TR, TL, C,
												 R, L, T, B, *) where B=bottom, T=top, L=left,
												 R=right, C=centre, *=tile)
												 *or*
												 an absolute position in pixels (from top-left
												 corner of canvas to top-left corner of overlay)
												 in format {xoffset}x{yoffset} (eg: "10x20")
												 note: this is center position of image if <x>
												 and <y> are set
											 <o> is opacity from 0 (transparent) to 100 (opaque)
												 (requires PHP v4.3.2, otherwise 100% opaque);
											 <x> and <y> are the edge (and inter-tile) margin in
												 pixels (or percent if 0 < (x|y) < 1)
												 *or*
												 if <a> is absolute-position format then <x> and
												 <y> represent maximum width and height that the
												 watermark image will be scaled to fit inside
											 <r> is rotation angle of overlaid watermark
										   - "wmt" (WaterMarkText)
											 [ex: &fltr[]=wmt|<t>|<s>|<a>|<c>|<f>|<o>|<m>|<n>|<b>|<O>|<x>]
											 where:
											 <t> is the text to use as a watermark;
												 URLencoded Unicode HTMLentities must be used for
												   characters beyond chr(127). For example, the
												   "eighth note" character (U+266A) is represented
												   as "&#9834;" and then urlencoded to "%26%239834%3B"
												 Any instance of metacharacters will be replaced
												 with their calculated value. Currently supported:
												   ^Fb = source image filesize in bytes
												   ^Fk = source image filesize in kilobytes
												   ^Fm = source image filesize in megabytes
												   ^X  = source image width in pixels
												   ^Y  = source image height in pixels
												   ^x  = thumbnail width in pixels
												   ^y  = thumbnail height in pixels
												   ^^  = the character ^
											 <s> is the font size (1-5 for built-in font, or point
												 size for TrueType fonts);
											 <a> is the alignment (one of BR, BL, TR, TL, C, R, L,
												 T, B, * where B=bottom, T=top, L=left, R=right,
												 C=centre, *=tile);
												 note: * does not work for built-in font "wmt"
												 *or*
												 an absolute position in pixels (from top-left
												 corner of canvas to top-left corner of overlay)
												 in format {xoffset}x{yoffset} (eg: "10x20")
											 <c> is the hex color of the text;
											 <f> is the filename of the TTF file (optional, if
												 omitted a built-in font will be used);
											 <o> is opacity from 0 (transparent) to 100 (opaque)
												 (requires PHP v4.3.2, otherwise 100% opaque);
											 <m> is the edge (and inter-tile) margin in percent;
											 <n> is the angle
											 <b> is the hex color of the background;
											 <O> is background opacity from 0 (transparent) to
												 100 (opaque)
												 (requires PHP v4.3.2, otherwise 100% opaque);
											 <x> is the direction(s) in which the background is
												 extended (either 'x' or 'y' (or both, but both
												 will obscure entire image))
												 Note: works with TTF fonts only, not built-in
										   - "flip" [ex: &fltr[]=flip|x   or   &fltr[]=flip|y]
											 flip image on X or Y axis
										   - "ric" [ex: &fltr[]=ric|<x>|<y>]
											 rounds off the corners of the image (to transparent
											 for PNG output), where <x> is the horizontal radius
											 of the curve and <y> is the vertical radius
										   - "elip" [ex: &fltr[]=elip]
											 similar to rounded corners but more extreme
										   - "mask" [ex: &fltr[]=mask|filename.png]
											 greyscale values of mask are applied as the alpha
											 channel to the main image. White is opaque, black
											 is transparent.
										   - "bvl" (BeVeL) [ex: &fltr[]=bvl|<w>|<c1>|<c2>]
											 where <w> is the bevel width, <c1> is the hex color
											 for the top and left shading, <c2> is the hex color
											 for the bottom and right shading
										   - "bord" (BORDer) [ex: &fltr[]=bord|<w>|<rx>|<ry>|<c>
											 where <w> is the width in pixels, <rx> and <ry> are
											 horizontal and vertical radii for rounded corners,
											 and <c> is the hex color of the border
										   - "fram" (FRAMe) draws a frame, similar to "bord" but
											 more configurable
											 [ex: &fltr[]=fram|<w1>|<w2>|<c1>|<c2>|<c3>]
											 where <w1> is the width of the main border, <w2> is
											 the width of each side of the bevel part, <c1> is the
											 hex color of the main border, <c2> is the highlight
											 bevel color, <c3> is the shadow bevel color
										   - "drop" (DROP shadow)
											 [ex: &fltr[]=drop|<d>|<w>|<clr>|<a>]
											 where <d> is distance from image to shadow, <w> is
											 width of shadow fade (not yet implemented), <clr> is
											 the hex color of the shadow, and <a> is the angle of
											 the shadow (default=225)
										   - "crop" (CROP image)
											 [ex: &fltr[]=crop|<l>|<r>|<t>|<b>]
											 where <l> is the number of pixels to crop from the left
											 side of the resized image; <r>, <t>, <b> are for right,
											 top and bottom respectively. Where (0 < x < 1) the
											 value will be used as a percentage of width/height.
											 Left and top crops take precedence over right and
											 bottom values. Cropping will be limited such that at
											 least 1 pixel of width and height always remains.
										   - "rot" (ROTate)
											 [ex: &fltr[]=rot|<a>|<b>]
											 where <a> is the rotation angle in degrees; <b> is the
											 background hex color. Similar to regular "ra" parameter
											 but is applied in filter order after regular processing
											 so you can rotate output of other filters.
										   - "size" (reSIZE)
											 [ex: &fltr[]=size|<x>|<y>|<s>]
											 where <x> is the horizontal dimension in pixels, <y> is
											 the vertical dimension in pixels, <s> is boolean whether
											 to stretch (if 1) or resize proportionately (0, default)
											 <x> and <y> will be interpreted as percentage of current
											 output image size if values are (0 < X < 1)
											 NOTE: do NOT use this filter unless absolutely necessary.
											 It is only provided for cases where other filters need to
											 have absolute positioning based on source image and the
											 resultant image should be resized after other filters are
											 applied. This filter is less efficient than the standard
											 resizing procedures.
										   - "stc" (Source Transparent Color)
											 [ex: &fltr[]=stc|<c>|<n>|<x>]
											 where <c> is the hex color of the target color to be made
											 transparent; <n> is the minimum threshold in percent (all
											 pixels within <n>% of the target color will be 100%
											 transparent, default <n>=5); <x> is the maximum threshold
											 in percent (all pixels more than <x>% from the target
											 color will be 100% opaque, default <x>=10); pixels between
											 the two thresholds will be partially transparent.
								        */
								'xto', 	// EXIF Thumbnail Only - set to only extract EXIF thumbnail and not do any additional processing
								'ra', 	// Rotate by Angle: angle of rotation in degrees positive = counterclockwise, negative = clockwise
								'ar', 	/* Auto Rotate: set to "x" to use EXIF orientation
										   stored by camera. Can also be set to "l" or "L"
										   for landscape, or "p" or "P" for portrait. "l"
										   and "P" rotate the image clockwise, "L" and "p"
										   rotate the image counter-clockwise.
										*/
								'aoe', 	/* Output Allow Enlarging - override the setting for
									       $CONFIG['output_allow_enlarging'] (1=on, 0=off)
    									   ("far" and "iar" both override this and allow output
       										larger than input)
										*/
								'far', 	/* Force Aspect Ratio - image will be created at size
										   specified by "w" and "h" (which must both be set).
										   Alignment: L=left,R=right,T=top,B=bottom,C=center
										   BL,BR,TL,TR use the appropriate direction if the
										   image is landscape or portrait.
										*/
								'iar', 	/* Ignore Aspect Ratio - disable proportional resizing
										   and stretch image to fit "h" & "w" (which must both
										   be set).  (1=on, 0=off)  (overrides "far")
										*/
								'maxb', /* MAXimum Byte size - output quality is auto-set to
										   fit thumbnail into "maxb" bytes  (compression
										   quality is adjusted for JPEG, bit depth is adjusted
										   for PNG and GIF)
										*/
								'down', /* filename to save image to. If this is set the
										   browser will prompt to save to this filename rather
										   than display the image
										*/
								'phpThumbDebug', //  
								'hash', //  deprecated I think...
								'md5s', /*  MD5 hash of the source image -- if this parameter is
										   passed with the hash of the source image then the
										   source image is not checked for existence or
										   modification and the cached file is used (if
										   available). If 'md5s' is passed an empty string then
										   phpThumb.php dies and outputs the correct MD5 hash
										   value.  This parameter is the single-file equivalent
										   of 'cache_source_filemtime_ignore_*' configuration
										   paramters
										*/
								'sfn',  /* Source Frame Number - use this frame/page number for
									       multi-frame/multi-page source images (GIF, TIFF, etc)
										*/
								'dpi', 	// Dots Per Inch - input DPI setting when importing from vector image format such as PDF, WMF, etc
								'sia', 	/* Save Image As - default filename to save generated
      										image as. Specify the base filename, the extension
       										(eg: ".png") will be automatically added
										 */
								'nocache'//  ?
								);
	    
	
	    foreach ($params as $key=>$ptv) {
            if (in_array($key,$allowedGETparameters)) {
                // so we'll only build our GET string off what phpThumb wants
                 $src .= '&amp;'.$key.'='.$ptv;
            }
	    }
	//}
	
	//If we are in the production mode, display default image for the dead link images
	if(!DEVELOPMENT) {
		$src .= '&amp;err=' . PATH_RELATIVE. 'framework/core/assets/images/default_preview_notfound.gif';
	}
	
	$source = ' src="'.$src.'"';
    
    if (empty($params['return'])) {
    	echo '<img'.$id.$class.$source.$dims.$alt.$style.$title.$closing;
    } else {
    	return '<img'.$id.$class.$source.$dims.$alt.$style.$title.$rel.$closing;
    }
}

?>
