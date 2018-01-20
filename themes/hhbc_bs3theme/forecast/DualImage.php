<?php
#----------------------------------------------------------------------------------------
# program: DualImage
# purpose: act like the NWS DualImage.php to combine images for the new 6-hour forecast
#          by using existing .png templates and optionally writing PoP on the output image.
#
# calling GET-mode parameters on URL:
#
#    i=<imgname>    base name of left image
#    j=<imgname>    base name of right image
#    ip=nn          optional PoP nn=10...100 for left image
#    jp=nn          optional PoP nn=10...100 for right image
#
# Author:  Ken True - webmaster@saratoga-weather.org
#
# Version 1.00 - 06-Jul-2015 - initial release
# Version 1.01 - 22-Apr-2017 - changes for NWS website icon usage
#
#----------------------------------------------------------------------------------------
# Settings:
#
$imgBaseDir = './forecast/icon-templates/'; # directory for png image sources WITH trailing '/'
$outType    = 'jpg';          # =jpg (55x55) =png (86x86)
#
# end of settings
#----------------------------------------------------------------------------------------

# The list of image names from the NWS and how to process them:
$NWSICONS = array(
#  PoP flag controls if PoP is written on output or not.
#  ScePosition controls if image is pulled from Left, Middle or Right of source image
#  Text Name - optional, just a reminder of what it means
#  PoP?=[YN] | ScePosition=[LMR] | Text Name (optional)
  'bkn' => 'Y|L|Broken Clouds',
  'blizzard' => 'Y|L|Blizzard',
  'cold' => 'Y|L|Cold',
  'cloudy' => 'Y|L|Overcast (old cloudy)',
  'du' => 'N|M|Dust',
  'dust' => 'N|M|Dust (old)',
  'fc' => 'N|L|Funnel Cloud',
  'nsvrtsra' => 'N|L|Funnel Cloud (old)',
  'few' => 'Y|L|Few Clouds',
  'fg' => 'Y|R|Fog',
  'br' => 'Y|R|Fog / mist old',
  'fu' => 'N|L|Smoke',
  'smoke' => 'N|L|Smoke (old)',
  'fzra' => 'Y|L|Freezing rain',
  'fzrara' => 'Y|L|Rain/Freezing Rain (old)',
  'fzra_sn' => 'Y|L|Freezing Rain/Snow',
  'mix' => 'Y|L|Freezing Rain/Snow',
  'hi_bkn' => 'Y|L|Broken Clouds (old)',
  'hi_few' => 'Y|L|Few Clouds (old)',
  'hi_sct' => 'Y|L|Scattered Clouds (old)',
  'hi_skc' => 'N|L|Clear Sky (old)',
  'hi_nbkn' => 'Y|L|Night Broken Clouds (old)',
  'hi_nfew' => 'Y|L|Night Few Clouds (old)',
  'hi_nsct' => 'Y|L|Night Scattered Clouds (old)',
  'hi_nskc' => 'N|L|Night Clear Sky (old)',
  'hi_nshwrs' => 'Y|R|Night Showers',
  'hi_ntsra' => 'Y|L|Night Thunderstorm',
  'hi_shwrs' => 'Y|R|Showers',
  'hi_tsra' => 'Y|L|Thunderstorm',
  'hur_warn' => 'N|L|Hurrican Warning',
  'hur_watch' => 'N|L|Hurricane Watch',
  'hurr' => 'N|L|Hurrican Warning old',
  'hurr-noh' => 'N|L|Hurricane Watch old',
  'hz' => 'N|L|Haze',
  'hazy' => 'N|L|Haze old',
  'hot' => 'N|R|Hot',
  'ip' => 'Y|L|Ice Pellets',
  'minus_ra' => 'Y|L|Stopped Raining',
  'ra1' => 'N|L|Stopped Raining (old)',
  'mist' => 'Y|R|Mist (fog) (old)',
  'nbkn' => 'Y|L|Night Broken Clouds',
  'nblizzard' => 'Y|L|Night Blizzard',
  'ncloudy' => 'Y|L|Overcast night(old ncloudy)',
  'ncold' => 'Y|L|Night Cold',
  'ndu' => 'N|M|Night Dust',
  'nfc' => 'N|L|Night Funnel Cloud',
  'nfew' => 'Y|L|Night Few Clouds',
  'nfg' => 'Y|R|Night Fog',
  'nbr' => 'Y|R|Night Fog/mist (old)',
  'nfu' => 'N|L|Night Smoke',
  'nfzra' => 'Y|L|Night Freezing Rain',
  'nfzra_sn' => 'Y|L|Night Freezing Rain/Snow',
  'nip' => 'Y|L|Night Ice Pellets',
  'novc' => 'Y|L|Night Overcast',
  'nra' => 'Y|30|Night Rain',
  'nraip' => 'Y|M|Night Rain/Ice Pellets',
  'nra_fzra' => 'Y|30|Night Freezing Rain',
  'nmix' => 'Y|30|Night Freezing Rain/Snow (old)',
  'nra_sn' => 'Y|M|Night Snow',
  'nrasn' => 'Y|M|Night Snow (old)',
  'nsct' => 'Y|L|Night Scattered Clouds',
  'pcloudyn' => 'Y|L|Night Partly Cloudy (old)',
  'nscttsra' => 'Y|M|Night Scattered Thunderstorm',
  'nshra' => 'Y|8|Night Rain Showers',
  'nskc' => 'N|L|Night Clear',
  'nsn' => 'Y|L|Night Snow',
  'nsnip' => 'Y|L|Night Snow/Ice Pellets',
  'nsn_ip' => 'Y|L|Night Snow/Ice Pellets (old)',
  'ntor' => 'N|L|Night Tornado',
  'ntsra' => 'Y|8|Night Thunderstorm',
  'nwind_bkn' => 'Y|5|Night Windy/Broken Clouds',
  'nwind_few' => 'Y|5|Night Windy/Few Clouds',
  'nwind_ovc' => 'Y|5|Night Windy/Overcast',
  'nwind_sct' => 'Y|5|Night Windy/Scattered Clouds',
  'nwind_skc' => 'N|5|Night Windy/Clear',
  'nwind' => 'N|5|Night Windy/Clear (old)',
  'ovc' => 'Y|L|Overcast',
  'ra' => 'Y|30|Rain',
  'raip' => 'Y|M|Rain/Ice Pellets',
  'ra_fzra' => 'Y|30|Rain/Freezing Rain',
  'ra_sn' => 'Y|M|Rain/Snow',
  'rasn' => 'Y|M|Rain/Snow (old)',
  'sct' => 'Y|L|name',
  'pcloudy' => 'Y|L|Partly Cloudy (old)',
  'scttsra' => 'Y|M|name',
  'shra' => 'Y|10|Rain Showers',
  'shra2' => 'Y|10|Rain Showers (old)',
  'skc' => 'N|L|Clear',
  'sn' => 'Y|L|Snow',
  'snip' => 'Y|L|Snow/Ice Pellets',
  'sn_ip' => 'Y|L|Snow/Ice Pellets (old)',
  'tcu' => 'Y|L|Towering Cumulus (old)',
  'tor' => 'N|L|Tornado',
  'tsra' => 'Y|10|Thunderstorm',
  'tstormn' => 'Y|L|Thunderstorm night (old)',
  'ts_nowarn' => 'N|L|Tropical Storm',
  'ts_warn' => 'N|L|Tropical Storm Warning',
  'tropstorm-noh' => 'N|L|Tropical Storm old',
  'tropstorm' => 'N|L|Tropical Storm Warning old',
  'ts_watch' => 'N|L|Tropical Storm Watch',
  'ts_hur_flags' => 'Y|L|Hurrican Warning old',
  'ts_no_flag' => 'Y|L|Tropical Storm old',
  'wind_bkn' => 'Y|8|Windy/Broken Clouds',
  'wind_few' => 'Y|8|Windy/Few Clouds',
  'wind_ovc' => 'Y|8|Windy/Overcast',
  'wind_sct' => 'Y|8|Windy/Scattered Clouds',
  'wind_skc' => 'N|8|Windy/Clear',
  'wind' => 'N|L|Windy/Clear (old)',
  'na'       => 'N|L|Not Available',
);
# program constants.. do not change these:
$imgExt = '.png'; # all SOURCE image templates are .png -- DON'T change this!
$TTfont = $imgBaseDir . 'fradmcn.ttf'; // location/name of TTF font to write PoP on image
$imgOverlay = $imgBaseDir . 'overlay-86x15.png';  # for the semi-transparent overlay for numbers
$saveDir    = './forecast/images/'; # optional save images to this directory
$saveType = 'jpg'; # only used for saving png=86x86, jpg=55x55
$imgSizeX   = 86;             # construction image width in pixels DON'T CHANGE
$imgSizeY   = 86;             # construction image height in pixels DON'T CHANGE
# end of program constants
#----------------------------------------------------------------------------------------
# get/validate input parameters
#----------------------------------------------------------------------------------------
if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');

   readfile($filenameReal);
   exit;
}

if(isset($_GET['i']) and isset($NWSICONS[$_GET['i']]) ) {
	$leftIcon = trim($_GET['i']);
} else {
	$leftIcon = 'na';
}
list($leftDoPoP,$leftSelect,$leftName) = explode('|',$NWSICONS[$leftIcon].'|||');

if($leftDoPoP == 'Y' and isset($_GET['ip']) and preg_match('!^([0-9]{1,3})$!',$_GET['ip']) ) {
	$leftPoP = $_GET['ip'];
	if($leftPoP > 100) {$leftPoP = 100;}
	if($leftPoP < 10)  {$leftPoP = 0;  }
} else {
	$leftPoP = 0;
}

if(!isset($_GET['j']) and isset($_GET['i'])) { // icon gen
  $_GET['j'] = $_GET['i']; // fake for single icon generation
}

if(isset($_GET['j']) and isset($NWSICONS[$_GET['j']]) ) {
	$rightIcon = trim($_GET['j']);
} else {
	$rightIcon = 'na';
}
list($rightDoPoP,$rightSelect,$rightName) = explode('|',$NWSICONS[$rightIcon].'|||');

if($rightDoPoP == 'Y' and isset($_GET['jp']) and preg_match('!^([0-9]{1,3})$!',$_GET['jp'])) {
	$rightPoP = $_GET['jp'];
	if($rightPoP > 100) {$rightPoP = 100;}
	if($rightPoP < 10)  {$rightPoP = 0;  }
} else {
	$rightPoP = 0;
}

$doSaveImage = (isset($_GET['save']) and $_GET['save'] == 'yes')?true:false;
if($doSaveImage and isset($_GET['ip']) and preg_match('!^([0-9]{1,3})$!',$_GET['ip']) ) {
	$leftPoP = $_GET['ip'];
	if($leftPoP > 100) {$leftPoP = 100;}
	if($leftPoP < 10)  {$leftPoP = 0;  }
}

if(file_exists($imgBaseDir . $leftIcon . $imgExt)) {
	$timg = imagecreatefrompng($imgBaseDir . $leftIcon . $imgExt);
    $SceX = imagesx($timg);
    $SceY = imagesy($timg);
	$LIMG = imagecreatetruecolor($imgSizeX,$imgSizeY);
    imagecopyresampled($LIMG,$timg,0,0,0,0,$imgSizeX,$imgSizeY,$SceX,$SceY);
	imagedestroy($timg);
} else {
	$LIMG = false;
}

if(file_exists($imgBaseDir . $rightIcon . $imgExt)) {
	$timg = imagecreatefrompng($imgBaseDir . $rightIcon . $imgExt);
    $SceX = imagesx($timg);
    $SceY = imagesy($timg);
	$RIMG = imagecreatetruecolor($imgSizeX,$imgSizeY);
    imagecopyresampled($RIMG,$timg,0,0,0,0,$imgSizeX,$imgSizeY,$SceX,$SceY);
	imagedestroy($timg);
} else {
	$RIMG = false;
}

if(file_exists($imgOverlay)) {
	$OIMG = imagecreatefrompng($imgOverlay);
} else {
	$OIMG = false;
	exit("--Error: unable load overlay '$imgOverlay'\n");
}

#----------------------------------------------------------------------------------------
# start processing request
#----------------------------------------------------------------------------------------

# create our working output image canvas
$img = imagecreatetruecolor($imgSizeX, $imgSizeY); # our working copy here

# single image, 0, 1 or 2 PoP
if($LIMG and ($leftIcon == $rightIcon) ) { # single image one or 2 PoP

  imagecopy($img,$LIMG,0,0,0,0,$imgSizeX,$imgSizeY);
  
  if($leftPoP > 0 and $rightPoP == 0) { # single PoP provided
    add_PoP($img,'F',$leftPoP,$OIMG,$TTfont);
  } 
  if($leftPoP > 0 and $rightPoP > 0 ) { # two PoP provided
    $leftW = add_PoP($img,'L',$leftPoP,$OIMG,$TTfont);
    $rightW = add_PoP($img,'R',$rightPoP,$OIMG,$TTfont);
	if(abs($leftPoP-$rightPoP) >= 30) {# Draw the arrow
      $newblue = imagecolorallocate($img,0,72,123);      // new-BLUE
	  $imgX = imagesx($img);
	  $imgY = imagesy($img);
	  $lineStart = $leftW + 4;
	  $lineEnd   = $imgY-$rightW - 4;
	  $lineY     = $imgY-8;
	  
      #imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
	  imageline($img,$lineStart,$lineY,$lineEnd,$lineY,$newblue); # line
	  imageline($img,$lineEnd-3,$lineY-3,$lineEnd,$lineY,$newblue); # upper arrow
	  imageline($img,$lineEnd-3,$lineY+3,$lineEnd,$lineY,$newblue); # lower arrow
	}
  }

}

# dual image processing
if($LIMG and $RIMG  and ! ($leftIcon == $rightIcon) ) { # dual icons, 0, 1 or 2 PoP

  copy_image($img,'L',$LIMG,$leftSelect);
  copy_image($img,'R',$RIMG,$rightSelect);
  $leftW = 0;
  $rightW = 0;
  
  if($leftPoP > 0 and $rightPoP == 0 and $leftDoPoP == 'Y') { # single PoP provided
    $leftW = add_PoP($img,'L',$leftPoP,$OIMG,$TTfont);
  } 
  if($rightPoP > 0 and $leftPoP == 0 and $rightDoPoP == 'Y') { # single PoP provided
    $rightW = add_PoP($img,'R',$rightPoP,$OIMG,$TTfont);
  } 
  if($leftPoP > 0 and $rightPoP > 0 and $leftDoPoP == 'Y' and $rightDoPoP == 'Y') { # two PoP provided
    $leftW = add_PoP($img,'L',$leftPoP,$OIMG,$TTfont);
    $rightW = add_PoP($img,'R',$rightPoP,$OIMG,$TTfont);
  }
  if($leftIcon != $rightIcon ) {draw_separator($img);}
  
  if(($leftPoP > 0 and $leftPoP > 0) and abs($leftPoP-$rightPoP) >= 30 and
      $leftDoPoP == 'Y' and $rightDoPoP == 'Y') {# Draw the arrow
	$newblue = imagecolorallocate($img,0,72,123);      // new-BLUE
	$imgX = imagesx($img);
	$imgY = imagesy($img);
	$lineStart = $leftW + 6;
	$lineEnd   = $imgY-$rightW - 6;
	$lineY     = $imgY-8;
	
	#imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
	imageline($img,$lineStart,$lineY,$lineEnd,$lineY,$newblue); # line
	imageline($img,$lineEnd-3,$lineY-3,$lineEnd,$lineY,$newblue); # upper arrow
	imageline($img,$lineEnd-3,$lineY+3,$lineEnd,$lineY,$newblue); # lower arrow
	
  }
}


#----------------------------------------------------------------------------------------
# output the resulting image
#----------------------------------------------------------------------------------------

if($doSaveImage) { // only processed if the image is saved
	$lPoP = $leftPoP>0?$leftPoP:'';
	$rPoP = $rightPoP>0?$rightPoP:'';
	
	$fn = $leftIcon.$lPoP;
	if($rightIcon != 'na' and $leftIcon != $rightIcon) {$fn .= "-$rightIcon$rPoP"; }
	if(isset($_GET['usename'])) {$fn = $_GET['usename'].$lPoP.$rPoP; }

  if($saveType == 'jpg') {
	$timg = imagecreatetruecolor(55,55);
    imagecopyresampled($timg,$img,0,0,0,0,55,55,$imgSizeX,$imgSizeY);
	$fn .= '.jpg';
	imagejpeg($timg,$saveDir.$fn,95);
	imagedestroy($timg);
  } else {
	$fn .= '.png';
	imagepng($img,$saveDir.$fn);
  }
  
	print "Image saved to $fn\n";
  # end of save-image process
  
} elseif ($outType == 'png') {
	# image is already 86x86 .. just output it
	header('Content-type: image/png');
    imagepng($img);
} elseif ($outType == 'jpg') {
	# image has to be resized to 55x55 for .jpg
	$timg = imagecreatetruecolor(55,55);
    imagecopyresampled($timg,$img,0,0,0,0,55,55,$imgSizeX,$imgSizeY);
	header('Content-type: image/jpeg');
	imagejpeg($timg);
	imagedestroy($timg);
	
}

#----------------------------------------------------------------------------------------
# cleanup and exit
#----------------------------------------------------------------------------------------

imagedestroy($img);
if($OIMG) {imagedestroy($OIMG);}
if($LIMG) {imagedestroy($LIMG);}
if($RIMG) {imagedestroy($RIMG);}

return(0);

#----------------------------------------------------------------------------------------
# functions
#----------------------------------------------------------------------------------------

function add_PoP( $img, $Position, $PoP, $OIMG, $TTfont) {
# add a PoP stamp to the image
# arguments: image, [L|F|R], PoPvalue, Overlay, TTfont path/name
  error_reporting(E_ALL);

  $PoPtest = preg_replace("|[^\d]+|is",'',$PoP); // replace non digits

  if ($PoPtest < 10) { return(0); } // nothing to write
  
  $imgX = imagesx($img);
  $imgY = imagesy($img);
  
// load existing image and write PoP on it
  $newblue = imagecolorallocate($img,0,72,123);      // new-BLUE
  $white = imagecolorallocate($img,255,255,240);  // WHITE 
  $fontcolor = $newblue;
  $angle = 0;
  $ttfsize = 11;

  if ($PoPtest >= 10) {
	$oY = imagesy($OIMG);
	$oX = imagesx($OIMG);
	if($Position == 'F') { # full stripe, right PoP legend
	  imagecopymerge($img,$OIMG,0,$imgY-$oY,0,0,$oX,$oY,80); 
	  $ttb = calculateTextBox($PoP . "%",$TTfont,$ttfsize,$angle);
	  $x = $imgX-$ttb['width']-2; // starting X position
	  $y = $imgY-3; // starting Y position
	  imagettftext ($img, $ttfsize, $angle, $x, $y, $fontcolor, $TTfont, $PoP . "%");

	} elseif($Position == 'R') { # right half stripe, right PoP Legend
# imagecopymerge ( resource $dst_im , resource $src_im , 
#       int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h , int $pct )

	  imagecopymerge($img,$OIMG,$imgX/2,$imgY-$oY,0,0,$oX,$oY,80); 
	  $ttb = calculateTextBox($PoP . "%",$TTfont,$ttfsize,$angle);
	  $x = $imgX-$ttb['width']-2; // starting X position
	  $y = $imgY-3; // starting Y position
	  imagettftext ($img, $ttfsize, $angle, $x, $y, $fontcolor, $TTfont, $PoP . "%");
		
	} else { # left half stripe, left PoP Legend

	  imagecopymerge($img,$OIMG,0,$imgY-$oY,0,0,$oX/2,$oY,80); 
	  $ttb = calculateTextBox($PoP . "%",$TTfont,$ttfsize,$angle);
	  $x = 2; // starting X position
	  $y = $imgY-3; // starting Y position
	  imagettftext ($img, $ttfsize, $angle, $x, $y, $fontcolor, $TTfont, $PoP . "%");
	}
  }

 
return($ttb['width']); # return the width of the text box

} # end add_PoP()

#----------------------------------------------------------------------------------------

function draw_separator ($img) {
# purpose -- draw the vertical separator line(s) in middle of image
  $imgX = imagesx($img);
  $imgY = imagesy($img);
  $white = imagecolorallocate($img,255,255,240);  // WHITE 
  $black = imagecolorallocate($img,0,0,0);        // BLACK 
  $gray  = imagecolorallocate($img,102,102,102);  // GRAY 
  
  $x = $imgX/2 - 2;
  
  imageline($img,$x,0,$x,$imgY,$black); # line
  imagefilledrectangle($img,$x+1,0,$x+4,$imgY,$gray); # line
  imageline($img,$x+2,2,$x+2,$imgY-2,$white); # line

} # end draw_separator()

#----------------------------------------------------------------------------------------

function calculateTextBox($text,$fontFile,$fontSize,$fontAngle) { 
     /************ 
     simple function that calculates the *exact* bounding box (single pixel precision). 
     The function returns an associative array with these keys: 
     left, top:  coordinates you will pass to imagettftext 
     width, height: dimension of the image you have to create 
     *************/ 
     $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text); 
     $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6])); 
     $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6])); 
     $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7])); 
     $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
     
     return array( 
      "left"   => abs($minX) - 1, 
      "top"    => abs($minY) - 1, 
      "width"  => $maxX - $minX, 
      "height" => $maxY - $minY, 
      "box"    => $rect 
     ); 
 } # end calculateTextBox()

#----------------------------------------------------------------------------------------
 
function copy_image($img,$side,$sceimg,$from) {
  # destination, side-to-use, source img, extract-from-L/M/R
  $imgX = imagesx($img);
  $imgY = imagesy($img);
  
  $sceX = imagesx($sceimg);
  $sceY = imagesy($sceimg);
  
  if(preg_match('!^\d+$!',$from)) { # source 1/2 image at starting X offset
	  $frX =$from;
	  $frY = $imgY;
	  $frW = $imgX/2;
  } elseif($from == 'L') { # source from left half of image
	  $frX =0;
	  $frY = $imgY;
	  $frW = $imgX/2;
	  
  } elseif ($from == 'R') { # source from right half of image
	  $frX = $imgX/2;
	  $frY = $imgY;
	  $frW = $imgX/2;
  } else { # source from middle of image
      $frX = $imgX/4;
	  $frY = $imgY;
	  $frW = $imgX/2;
  }
 
  if($side == 'L') { # draw to left-side
	imagecopy($img,$sceimg,0,0,$frX,0,$frW,$imgY); 
  
  } elseif($side == 'R') { # draw to right-side
	imagecopy($img,$sceimg,$imgX/2,0,$frX,0,$frW,$imgY); 
  }
	
} # end copy_image()
#----------------------------------------------------------------------------------------
?>