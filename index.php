<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','index.php');

/**
 * @param $buffer
 * @param $mode
 * @return string
 */
function epb($buffer, $mode) {
//    @ob_gzhandler($buffer, $mode);
    @ob_gzhandler($buffer);
//    return $buffer; // uncomment if you're messing with output buffering so errors show. ~pb
    return expProcessBuffer($buffer);  // add/process css & jscript for page
}

ob_start('epb');
$microtime_str = explode(' ',microtime());
$i_start = $microtime_str[0] + $microtime_str[1];

// Initialize the Exponent Framework
require_once('exponent.php');

//active global timer if in DEVELOPMENT mode
if(DEVELOPMENT) $timer = new expTimer();    

// if the user has turned on sef_urls then we need to route the request, otherwise we can just 
// skip it and default back to the old way of doing things.
$router->routeRequest();

// initialize this users cart if they have ecom installed.
// define whether or not ecom is enabled
//if ($db->selectValue('modstate', 'active', 'module="storeController"') ||
//  $db->selectValue('modstate', 'active', 'module="eventregistrationController"') ||
//  $db->selectValue('modstate', 'active', 'module="donationController"') || FORCE_ECOM) {
if ($db->selectValue('modstate', 'active', 'module="store"') ||
  $db->selectValue('modstate', 'active', 'module="eventregistration"') ||
  $db->selectValue('modstate', 'active', 'module="donation"') || FORCE_ECOM) {
    define('ECOM',1);
    $order = order::getUserCart();
    // global store config
    // We're forcing the location. Global store setting will always have this loc
    $cfg = new stdClass();
    $cfg->mod = "ecomconfig";
    $cfg->src = "@globalstoresettings";
    $cfg->int = "";
    $storeConfig = new expConfig($cfg);
} else {
    define('ECOM',0);
}

if (isset($_GET['id']) && !is_numeric($_GET['id'])) $_GET['id'] = intval($_GET['id']);
if ($db->havedb) {
    $section = $router->getSection();
    $sectionObj = $router->getSectionObj($section);
    if ($sectionObj->alias_type == 1) {  // asking for an external link url instead of exponent
        redirect_to(substr($sectionObj->external_link, 4) == 'http' ? $sectionObj->external_link : 'http://' . $sectionObj->external_link);
    }
}
if (ENABLE_TRACKING) $router->updateHistory($section);

// set the output header
header("Content-Type: text/html; charset=".LANG_CHARSET);

// Check to see if we are in maintenance mode.
if (MAINTENANCE_MODE && !$user->isAdmin() && (!isset($_REQUEST['controller']) || $_REQUEST['controller'] != 'login') && !expJavascript::inAjaxAction()) {
	//only admins/acting_admins are allowed to get to the site, all others get the maintenance view
	$template = new standalonetemplate('_maintenance');
	$template->output();
} else {
	if (MAINTENANCE_MODE > 0) flash('error', gt('Maintenance Mode is Enabled'));
	//the default user is anonymous
//	if (!expSession::loggedIn()) {
		//TODO: Maxims initial anonymous user implementation
		//user::login("anonymous", "anonymous");
//	}

	// check to see if we need to install or upgrade the system
	expVersion::checkVersion();

	// Handle sub themes
	$page = expTheme::getTheme();

	// If we are in a printer friendly request then we need to change to our printer friendly subtheme
	if (PRINTER_FRIENDLY == 1 || EXPORT_AS_PDF == 1) {
		expSession::set("uilevel",0);
		$pftheme = expTheme::getPrinterFriendlyTheme();  	// get the printer friendly theme
		$page = $pftheme == null ? $page : $pftheme;		// if there was no theme found then just use the current subtheme
	}
 
	if (is_readable($page)) {
		if (!expJavascript::inAjaxAction()) {
			include_once($page);
			expTheme::satisfyThemeRequirements();
		} else {
			expTheme::runAction();
		}
	} else {
		echo sprintf(gt('Page "%s" not readable.'), $page);
	}

	if (PRINTER_FRIENDLY == 1 || EXPORT_AS_PDF == 1) {
		expSession::un_set('uilevel');
	}
}

//$microtime_str = explode(' ',microtime());
//$i_end = $microtime_str[0] + $microtime_str[1];
//echo "\r\n<!--".sprintf(gt('Execution time : %d seconds'),round($i_end - $i_start,4)).'-->';

if (EXPORT_AS_PDF == 1) {
    $content = ob_get_clean();

    // convert to PDF
    $pdf = new expHtmlToPDF2('A4',EXPORT_AS_PDF_LANDSCAPE?'landscape':'portrait',$content);
    $pdf->createpdf(HTML2PDF_OUTPUT?'D':'I',$sectionObj->name.".pdf");
    echo '<script type="text/javascript">
        <!--
        setTimeout("self.close();",10000);
        //-->
        </script>';  //FIXME timeout before closing an empty pdf or html2pdf error window
} else {
    ob_end_flush();
}

?>