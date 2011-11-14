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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {rsslink} function plugin
 *
 * Type:     function<br>
 * Name:     rsslink<br>
 * Purpose:  create an rss link
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_rsslink($params,&$smarty) {
	$loc = $smarty->getTemplateVars('__loc');
	if (!isset($params['module'])) $params['module'] = $loc->mod;
	if (!isset($params['src'])) $params['src'] = $loc->src;
	if (!isset($params['int'])) $params['int'] = $loc->int;
	
	echo expCore::makeRSSLink($params);
}

?>
