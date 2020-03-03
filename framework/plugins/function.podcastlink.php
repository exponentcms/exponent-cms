<?php

##################################################
#
# Copyright (c) 2004-2020 OIC Group, Inc.
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
 * Smarty {podcastlink} function plugin
 *
 * Type:     function<br>
 * Name:     podcastlink<br>
 * Purpose:  make a podcast link
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_podcastlink($params,&$smarty) {
	$loc = $smarty->getTemplateVars('__loc');
	if (!isset($params['module'])) $params['module'] = $loc->mod;
	if (!isset($params['src'])) $params['src'] = $loc->src;
	if (!isset($params['int'])) $params['int'] = $loc->int;

	echo expCore::makePodcastLink($params);
}

?>
