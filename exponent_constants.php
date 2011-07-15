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

if (!defined('DISPLAY_THEME')) {
	/* exdoc
	 * The directory and class name of the current active theme.  This may be different
	 * than the configure theme (DISPLAY_THEME_REAL) due to previewing.
	 */
	define('DISPLAY_THEME',DISPLAY_THEME_REAL);
}

if (!defined('THEME_ABSOLUTE')) {
	/* exdoc
	 * The absolute path to the current active theme's files.  This is similar to the BASE constant
	 */
	define('THEME_ABSOLUTE',BASE.'themes/'.DISPLAY_THEME.'/'); // This is the recommended way
}

if (!defined('THEME_RELATIVE')) {
	/* exdoc
	 * The relative web path to the current active theme.  This is similar to the PATH_RELATIVE constant.
	 */
	define('THEME_RELATIVE',PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/');
}

if (!defined('JS_FULL')) {
	/* exdoc
	 * The absolute path to Exponent's core javascript.
	 */
	define('JS_FULL',URL_FULL.'framework/core/js/');
}

// Initialize the theme subsystem
//if (!defined('SYS_THEME')) require_once(BASE.'subsystems/theme.php');

// iconset base
if (!defined('ICON_RELATIVE')) {
	
	define('ICON_RELATIVE', PATH_RELATIVE . 'framework/core/assets/images/');
	
	//DEPRECATED: old directory, inconsistent naming
	/*if (is_readable(THEME_ABSOLUTE . 'icons/')) {
		/* exdoc
		 * The relative web path to the current icon set.  If an icons/ directory exists directly
		 * underneath the theme's directory, that is used.	Otherwise, the system falls back to
		 * the iconset directory in the root of the Exponent directory.
		define('ICON_RELATIVE', THEME_RELATIVE . 'icons/');
	} else 
		Commented out compat layer for < 0.96.6 version.  All icons should be in common/skin  ~phillip Ball
	
	
	if(is_readable(THEME_ABSOLUTE . "images/icons/")){
		define('ICON_RELATIVE',THEME_RELATIVE . 'images/icons/');
	} else {
		define('ICON_RELATIVE', PATH_RELATIVE . 'themes/common/images/icons/');
	}
	*/
}

if (!defined('MIMEICON_RELATIVE')) {
	//DEPRECATED: old directory, inconsitent naming
    // if (is_readable(THEME_ABSOLUTE . 'mimetypes/')) {
		/* exdoc
		 * The relative web path to the current MIME icon set.	If a mimetypes/ directory
		 * exists directly underneath the theme's directory, then that is used.	 Otherwise, the
		 * system falls back to the iconset/mimetypes/ directory in the root of the Exponent directory.
		 */
    //  define('MIMEICON_RELATIVE', THEME_RELATIVE . 'mimetypes/');
    // } else if(is_readable(THEME_ABSOLUTE . "images/icons/mimetypes" )){
    //  define('MIMEICON_RELATIVE', THEME_RELATIVE . "images/icons/mimetypes/");
    // } else {
    //  define('MIMEICON_RELATIVE', PATH_RELATIVE . 'themes/common/images/icons/mimetypes/');
    // }
    define('MIMEICON_RELATIVE', PATH_RELATIVE . 'framework/core/assets/images/mimetypes/');
}

if (!defined('YUI3_PATH')) {
    /*
	 *  YUI 3 Version Constant Constant
	 *
	 * Changing the version here lets Exponent adjust where 
	 */
	define('YUI3_VERSION', '3.3.0');
	define('YUI3_PATH', PATH_RELATIVE.'external/lissa/'.YUI3_VERSION.'/build/');
	define('YUI3_URL', URL_FULL.'external/lissa/'.YUI3_VERSION.'/build/');
}

if (!defined('YUI2_PATH')) {
	define('YUI2_VERSION', '2.8.0r4');
	define('YUI2_PATH', PATH_RELATIVE.'external/lissa/'.YUI2_VERSION.'/build/');
	define('YUI2_URL', URL_FULL.'external/lissa/'.YUI2_VERSION.'/build/');
}

?>