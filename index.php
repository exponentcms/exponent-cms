<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
//    @ob_gzhandler($buffer);
//    return $buffer; // uncomment if you're messing with output buffering so errors show. ~pb
    return expProcessBuffer($buffer);  // add/process css & jscript for page
}

ob_start('epb');

// Initialize the Exponent Framework
require_once('exponent.php');

//activate global timer if in DEVELOPMENT mode
if(DEVELOPMENT)
	$timer = new expTimer();

// if the user has turned on sef_urls then we need to route the request, otherwise we can just
// skip it and default back to the old way of doing things.
if ($db->havedb) {
    $router->routeRequest();
}

// define whether or not ecom is enabled & initialize this users cart if they have ecom installed.
if ($db->havedb && ecom_active()) {
    define('ECOM',1);
    // allow custom billing & shipping calculators
    if (file_exists(BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/')) {
        array_unshift($auto_dirs, BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/billingcalculators');
        array_unshift($auto_dirs, BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/shippingcalculators');
    }
    $order = order::getUserCart();  // set global store $order
} else {
    define('ECOM',0);
}

if ($db->havedb) {
    $section = $router->getSection();
    $sectionObj = $router->getSectionObj($section);
    if ($sectionObj->alias_type == 1) {  // asking for an external link url instead of exponent
        redirect_to(substr($sectionObj->external_link, 0, 4) === 'http' ? $sectionObj->external_link : 'http://' . $sectionObj->external_link);
    }
}
if (ENABLE_TRACKING)
	$router->updateHistory($section);

// set the output header
if (expJavascript::requiresJSON()) {
	header("Content-Type: application/json; charset=".LANG_CHARSET);
} else {
	header("Content-Type: text/html; charset=".LANG_CHARSET);
}

// check to see if we need to install or upgrade the system and confirm the db is up
expVersion::checkVersion();

// Check to see if we are in maintenance mode.
//if (MAINTENANCE_MODE && !$user->isAdmin() && (!isset($_REQUEST['controller']) || $_REQUEST['controller'] != 'login') && !expJavascript::inAjaxAction()) {
if (MAINTENANCE_MODE && !$user->isAdmin() && !expJavascript::inAjaxAction() && !(!empty($_REQUEST['controller']) && $_REQUEST['controller'] === 'login' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'login')) {
	//only admins/acting_admins are allowed to get to the site, all others get the maintenance view
    if (empty($framework)) {
        $framework = expSession::get('framework');
    }
//    expCore::setup_autoload($framework);
	$template = new standalonetemplate('_maintenance');
    if (!empty($_REQUEST['controller']) && $_REQUEST['controller'] === 'login') {
        $template->assign("login", true);
    }
	$template->output();
} else {
	if (MAINTENANCE_MODE > 0) flash('error', gt('Maintenance Mode is Enabled'));

	// Handle sub themes
	$page = expTheme::getTheme();

	// If we are in a printer friendly request then we need to change to our printer friendly subtheme
	if (PRINTER_FRIENDLY == 1 || EXPORT_AS_PDF == 1) {
		expSession::set("uilevel", UILEVEL_PREVIEW);
		$pftheme = expTheme::getPrinterFriendlyTheme();  	// get the printer friendly theme
		$page = $pftheme == null ? $page : $pftheme;		// if there was no theme found then just use the current subtheme
	}

	if (is_readable($page)) {
        define('PREVIEW_READONLY',0); // for mods
//      	define('SELECTOR',0);

		if (!expJavascript::inAjaxAction()) {
			include($page);
			expTheme::satisfyThemeRequirements();
		} else {  // ajax request
            if (empty($framework)) {
                $framework = expSession::get('framework');
            }
            expCore::setup_autoload($framework);
			expTheme::runAction();
		}
	} else {
		echo sprintf(gt("Page '%s' not readable."), $page);
	}

	if (PRINTER_FRIENDLY == 1 || EXPORT_AS_PDF == 1) {
		expSession::un_set('uilevel');
	}
}

if (DEBUG_HISTORY && $user->isAdmin() && !expJavascript::inAjaxAction())
	echo expHistory::print_history();
//write page build/load time if in DEVELOPMENT mode with logging
if (DEVELOPMENT && LOGGER)
	eLog(expDateTime::duration(0, $timer->mark(), true) . ' - ' . $section . '/' . $sectionObj->sef_name, gt('LOAD TIME'));

if (EXPORT_AS_PDF == 1) {
    $content = ob_get_clean();
	$content = expProcessBuffer($content);  // we didn't actually process the output buffer by adding styles

    // convert to PDF
    $pdf = new expHtmlToPDF(HTMLTOPDF_PAPER,EXPORT_AS_PDF_LANDSCAPE?'landscape':'portrait',$content);
    $pdf->createpdf(HTMLTOPDF_OUTPUT?'D':'I',$sectionObj->name.".pdf");
    echo '<script type="text/javascript">
        <!--
        setTimeout("self.close();",10000);
        //-->
        </script>';  //FIXME timeout before closing an empty pdf or html2pdf error window
} else {
    ob_end_flush();
}
expSession::un_set('force_less_compile');  // remove flag at when page finishes

?>