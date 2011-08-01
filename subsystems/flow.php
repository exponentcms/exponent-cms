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

/* exdoc
 * The definition of this constant lets other parts
 * of the system know that the Flow Subsystem
 * has been included for use.
 * @node Subsystems:Flow
 */
//define('SYS_FLOW',1);

/* exdoc
 * Flow Type Specifier : None
 * @node Subsystems:Flow
 */
define('SYS_FLOW_NONE',	 0);

/* exdoc
 * Flow Type Specifier : Public Access
 * @node Subsystems:Flow
 */
define('SYS_FLOW_PUBLIC',	 1);

/* exdoc
 * Flow Type Specifier : Protected Access
 * @node Subsystems:Flow
 */
define('SYS_FLOW_PROTECTED', 2);

/* exdoc
 * Flow Type Specifier : Sectional Page
 * @node Subsystems:Flow
 */
define('SYS_FLOW_SECTIONAL', 1);

/* exdoc
 * Flow Type Specifier : Action Page
 * @node Subsystems:Flow
 */
define('SYS_FLOW_ACTION',	 2);



$SYS_FLOW_REDIRECTIONPATH = 'exponent_default';

/* exdoc
 * Saves the current URL in a persistent session, to be used later.
 *
 * @param integer $access_level The access level of the current page.
 *  Either SYS_FLOW_PUBLIC or SYS_FLOW_PROTECTED
 * @param integer $url_type The type of URSL being set.  Either
 *  SYS_FLOW_SECTIONAL or SYS_FLOW_ACTION
 * @node Subsystems:Flow
 */
function exponent_flow_set($access_level,$url_type) {
	global $SYS_FLOW_REDIRECTIONPATH;
	global $router;
	//echo '<h1>setting flow</h1>'.$router->current_url;	
	if ($access_level == SYS_FLOW_PUBLIC) {
		exponent_sessions_set($SYS_FLOW_REDIRECTIONPATH.'_flow_' . SYS_FLOW_PROTECTED . '_' . $url_type, $router->current_url);
		exponent_sessions_set($SYS_FLOW_REDIRECTIONPATH.'_flow_last_' . SYS_FLOW_PROTECTED, $router->current_url);
	}
	exponent_sessions_set($SYS_FLOW_REDIRECTIONPATH.'_flow_' . $access_level . '_' . $url_type, $router->current_url);
	exponent_sessions_set($SYS_FLOW_REDIRECTIONPATH.'_flow_last_' . $access_level, $router->current_url);
	
	//FIXME:  Glue code to try to get new hisotry and old flow to play nicely together.
	expHistory::set('viewable', $router->params);
}

/* exdoc
 * Looks through persistent session data and returns the last URL set
 * for a specific type.  If the type is set to SYS_FLOW_NONE, then either
 * SYS_FLOW_ACTION or SYS_FLOW_SECTIONAL will be retrieved.
 *
 * @param integer $url_type The type of URL to retrieve, Either
 *   SYS_FLOW_SECTIONAL or SYS_FLOW_ACTION
 * @node Subsystems:Flow
 */
function exponent_flow_get($url_type = SYS_FLOW_NONE) {
#	global $SYS_FLOW_REDIRECTIONPATH;
#	$access_level = (exponent_sessions_loggedIn() ? SYS_FLOW_PROTECTED : SYS_FLOW_PUBLIC);
#	if (!exponent_sessions_isset($SYS_FLOW_REDIRECTIONPATH.'_flow_last_'.$access_level)) return URL_FULL;
#	switch ($url_type) {
#		case SYS_FLOW_NONE:
#			return exponent_sessions_get($SYS_FLOW_REDIRECTIONPATH.'_flow_last_' . $access_level);
#		case SYS_FLOW_SECTIONAL:
#		case SYS_FLOW_ACTION:
#			return exponent_sessions_get($SYS_FLOW_REDIRECTIONPATH.'_flow_' . $access_level . '_' . $url_type);
#	}
	
	return expHistory::getLastNotEditable();
}

/* exdoc
 * Looks at the persistent session data to figure out what the last 'valid' URL visited
 * was, and then redirects.  If the optional $url_type parameter is specified as anything
 * other than SYS_FLOW_NONE, then only that type of URL will be used for the redirection.
 *
 * @param integer $url_type The type of URL to retrieve, Either
 *   SYS_FLOW_SECTIONAL or SYS_FLOW_ACTION
 * @node Subsystems:Flow
 */
function exponent_flow_redirect($url_type = SYS_FLOW_NONE) {
#	global $SYS_FLOW_REDIRECTIONPATH;
#	$access_level = (exponent_sessions_loggedIn() ? SYS_FLOW_PROTECTED : SYS_FLOW_PUBLIC);
#	// Fallback to the default redirection path in strange edge cases.
#	if (!exponent_sessions_isset($SYS_FLOW_REDIRECTIONPATH.'_flow_last_'.$access_level)) $SYS_FLOW_REDIRECTIONPATH='exponent_default';
#	$url = '';
#	//die(eDebug($_SESSION));
#	switch ($url_type) {
#		case SYS_FLOW_NONE:
#			$url = exponent_sessions_get($SYS_FLOW_REDIRECTIONPATH . '_flow_last_' . $access_level);
#			break;
#		case SYS_FLOW_SECTIONAL:
#		case SYS_FLOW_ACTION:
#			$url = exponent_sessions_get($SYS_FLOW_REDIRECTIONPATH . '_flow_' . $access_level . '_' . $url_type);
#			break;
#	}

#	if ($url == '') {
#		$url = URL_FULL.'index.php?section='.SITE_DEFAULT_SECTION;
#	}
#	if (DEVELOPMENT >= 2) {
#		echo '<a href="'.$url.'">'.$url.'</a>';
#	} else {
#		header("Location: $url");
#	}
#	exit('Redirecting...');

    expHistory::back();
}

function exponent_flow_redirecto_login($redirecturl) {
	$redirecturl = empty($redirecturl) ? exponent_flow_get() : $redirecturl;
	exponent_sessions_set('redirecturl',$redirecturl);
	redirect_to(array('module'=>'loginmodule', 'action'=>'loginredirect'));
}

?>
