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
/** @define "BASE" "." */

if (!defined('EXPONENT')) exit('');
function epb($buffer, $mode) {
    //@ob_gzhandler($buffer, $mode);
    @ob_gzhandler($buffer);
    //return $buffer; // uncomment if you're messing with output buffering so errors show. ~pb
    return expProcessBuffer($buffer);
}

ob_start('epb');
$microtime_str = explode(' ',microtime());
$i_start = $microtime_str[0] + $microtime_str[1];

$section = (expSession::is_set('last_section') ? expSession::get('last_section') : SITE_DEFAULT_SECTION);
$section = $db->selectObject('section','id='.$section);

// Handle sub themes
$page = ($section && $section->subtheme != '' && is_readable('themes/'.DISPLAY_THEME.'/subthemes/'.$section->subtheme.'.php') ?
	'themes/'.DISPLAY_THEME.'/subthemes/'.$section->subtheme.'.php' :
	'themes/'.DISPLAY_THEME.'/index.php'
);
if (is_readable(BASE.$page)) {
	define('PREVIEW_READONLY',1); // for mods
	define('SELECTOR',1);
//	$SYS_FLOW_REDIRECTIONPATH='source_selector';

	$source_select = array();
	if (expSession::is_set('source_select')) $source_select = expSession::get('source_select');
	$count_orig = count($source_select);
	
	if (isset($_REQUEST['vview'])) {
		$source_select['view'] = $_REQUEST['vview'];
	} else if (!isset($source_select['view'])) {
		$source_select['view'] = '_sourcePicker';
	}
	
	if (isset($_REQUEST['vmod'])) {
		$source_select['module'] = $_REQUEST['vmod'];
	} else if (!isset($source_select['module'])) {
		$source_select['module'] = 'containermodule';
	}
	
	if (isset($_REQUEST['showmodules'])) {
		if (is_array($_REQUEST['showmodules'])) $source_select['showmodules'] = $_REQUEST['showmodules'];
		else if ($_REQUEST['showmodules'] == 'all') $source_select['showmodules'] = null;
		else $source_select['showmodules'] = explode(',',$_REQUEST['showmodules']);
	} else if (!isset($source_select['showmodules'])) {
		$source_select['showmodules'] = null;
	}
	
	if (isset($_REQUEST['dest'])) {
		$source_select['dest'] = $_REQUEST['dest'];
	} else if (!isset($source_select['dest'])) {
		$source_select['dest'] = null;
	}
	
	if (isset($_REQUEST['hideOthers'])) {
		$source_select['hideOthers'] = $_REQUEST['hideOthers'];
	} else if (!isset($source_select['hideOthers'])) {
		$source_select['hideOthers'] = 0;
	}
	
	expSession::set('source_select',$source_select);
	// Include the rendering page.
	include_once(BASE.$page);
	expTheme::satisfyThemeRequirements();
} else {
	echo sprintf(gt('Page').' "%s" '.gt('not readable.'),BASE.$page);
}

ob_end_flush();

?>