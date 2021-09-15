<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * Smarty {link} function plugin
 *
 * Type:     function<br>
 * Name:     link<br>
 * Purpose:  create a link
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_link($params,&$smarty) {
	$loc = $smarty->getTemplateVars('__loc');

	if (!empty($params['parse_attrs'])) {
	    $record = $params['record'];
	    foreach ($params['parse_attrs'] as $key => $value) {
	        $params[$key] = $value;
	        if ($params['showby']) {
                $prop = $params['showby'];
	            $params[$prop] = $record->$prop;
	            unset($params['showby']);
	        }
	    }
	   unset(
           $params['parse_attrs'],
           $params['record']
       );
	}
	// if the module wasn't passed in we will assume it is the same as the module for this view
	if (!isset($params['module']) && !isset($params['controller'])) {
	    $params['module'] = $loc->mod;
	}

	// make sure the module isn't really a controller
	if (!empty($params['module']) && expModules::controllerExists(!empty($params['module']))) {
		$params['controller'] = $params['module'];
		unset ($params['module']);
	}

	// guess the src if it is not set
	if (!isset($params['src'])) {
        if (!empty($params['controller']) || @call_user_func(array(expModules::getModuleClassName($loc->mod),'hasSources'))) {
			$params['src'] = $loc->src;
		} elseif (!empty($params['module']) || @call_user_func(array(expModules::getModuleClassName($loc->mod),'hasSources'))) {
            $params['src'] = $loc->src;
        }
	}

	// grab the int value
	if (!isset($params['int'])) $params['int'] = $loc->int;

	echo expCore::makeLink($params);
}

?>
