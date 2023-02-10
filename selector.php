<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

global $db;
if (!defined('EXPONENT')) exit('');
function epb($buffer, $mode) {
//    @ob_gzhandler($buffer, $mode);
//    return $buffer; // uncomment if you're messing with output buffering so errors show. ~pb
    return expProcessBuffer($buffer);
}

//Scrub Input
// strip out possible xss exploits via old school url
foreach ($_GET as $key=>$var) {
    if (is_string($var) && strpos($var,'">')) {
        unset(
            $_GET[$key],
            $_REQUEST[$key]
        );
    }
}
//fixme only old school url and forms have these variables here
// conventional method to ensure the 'id' is only an id
if (isset($_REQUEST['id'])) {
    $_REQUEST['id'] = (int)($_REQUEST['id']);
    if (isset($_GET['id']))
        $_GET['id'] = $_REQUEST['id'];
    if (isset($_POST['id']))
        $_POST['id'] = $_REQUEST['id'];
}
// do the same for the other id's
foreach ($_REQUEST as $key=>$var) {
    if (is_string($var) && strlen($key) >= 3 && strrpos($key,'_id',-3) !== false) {
        $_REQUEST[$key] = (int)($_REQUEST[$key]);
        if (isset($_GET[$key]))
            $_GET[$key] = $_REQUEST[$key];
        if (isset($_POST[$key]))
            $_POST[$key] = $_REQUEST[$key];
    }
    if ($key == 'src') {
        $_REQUEST[$key] = preg_replace("/[^A-Za-z0-9@\-_]/", '', $_REQUEST[$key]);
        if (isset($_GET[$key]))
            $_GET[$key] = $_REQUEST[$key];
        if (isset($_POST[$key]))
            $_POST[$key] = $_REQUEST[$key];
    }
}
expString::sanitize($_REQUEST);  // strip other exploits like sql injections

ob_start('epb');
$microtime_str = explode(' ',microtime());
$i_start = $microtime_str[0] + $microtime_str[1];

if (!expSession::is_set('last_section')) {
    expSession::set('last_section',SITE_DEFAULT_SECTION);
}
$section = $db->selectObject('section','id='.expSession::get('last_section'));

// Handle sub themes
$page = ($section && $section->subtheme != '' && is_readable('themes/'.DISPLAY_THEME.'/subthemes/'.$section->subtheme.'.php') ?
	'themes/'.DISPLAY_THEME.'/subthemes/'.$section->subtheme.'.php' :
	'themes/'.DISPLAY_THEME.'/index.php'
);
if (is_readable(BASE.$page)) {
	define('PREVIEW_READONLY',1); // for mods
	define('SELECTOR',1);

	$source_select = array();
	if (expSession::is_set('source_select')) $source_select = expSession::get('source_select');
	$count_orig = count($source_select);

	if (isset($_REQUEST['vview'])) {
		$source_select['view'] = expString::sanitize($_REQUEST['vview']);
	} else if (!isset($source_select['view'])) {
		$source_select['view'] = '_sourcePicker';
	}

	if (isset($_REQUEST['vmod'])) {
		$source_select['module'] = expString::sanitize($_REQUEST['vmod']);
	} else if (!isset($source_select['module'])) {
//		$source_select['module'] = 'containermodule';
        $source_select['module'] = 'container';
	}

	if (isset($_REQUEST['showmodules'])) {
		if (is_array($_REQUEST['showmodules'])) $source_select['showmodules'] = expString::sanitize($_REQUEST['showmodules']);
		else if ($_REQUEST['showmodules'] == 'all') $source_select['showmodules'] = null;
		else $source_select['showmodules'] = explode(',',$_REQUEST['showmodules']);
	} else if (!isset($source_select['showmodules'])) {
		$source_select['showmodules'] = null;
	}

	if (isset($_REQUEST['dest'])) {
		$source_select['dest'] = expString::sanitize($_REQUEST['dest']);
        if (stripos($source_select['dest'], 'javascript:') !== FALSE) {
            $source_select['dest'] = substr($source_select['dest'], 0, stripos($source_select['dest'], 'javascript:'));
        }
	} else if (!isset($source_select['dest'])) {
		$source_select['dest'] = null;
	}

//	if (isset($_REQUEST['hideOthers'])) {
//		$source_select['hideOthers'] = $_REQUEST['hideOthers'];
//	} else if (!isset($source_select['hideOthers'])) {
//		$source_select['hideOthers'] = 0;
//	}
    $source_select['hideOthers'] = !empty($_REQUEST['hideOthers']);

	expSession::set('source_select',$source_select);
    if (!defined('PRINTER_FRIENDLY')) define('PRINTER_FRIENDLY','0');
    if (!defined('EXPORT_AS_PDF')) define('EXPORT_AS_PDF','0');

	// Include the rendering page.
	include(BASE.$page);
	expTheme::satisfyThemeRequirements();
} else {
	echo sprintf(gt('Page')." '%s' ".gt('not readable.'),BASE.$page);
}

ob_end_flush();

?>