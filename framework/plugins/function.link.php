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

function smarty_function_link($params,&$smarty) {
	$loc = $smarty->_tpl_vars['__loc'];
	
	if ($params['parse_attrs']) {
	    $record = $params['record'];
	   foreach ($params['parse_attrs'] as $key => $value) {
	       $params[$key] = $value;
	       if ($params['showby']) {
	           $params[$params['showby']] = $record->$params['showby'];
	           unset($params['showby']);
	       }
	   }
	   unset($params['parse_attrs']);
	   unset($params['record']);
	}
	// if the module wasn't passed in we will assume it is the same as the module for this view
	if (!isset($params['module']) && !isset($params['controller'])) {
	    $params['module'] = $loc->mod;
	} 
	
	// make sure the module isn't really a controller
	if (controllerExists($params['module'])) {
		$params['controller'] = $params['module'];
		unset ($params['module']);
	}
	
	// guess the src if it is not set
	if (!isset($params['src'])) {
        if (!empty($params['controller']) || @call_user_func(array($loc->mod,'hasSources'))) {
			$params['src'] = $loc->src;
		}
	}
	
	// greb the int value
	if (!isset($params['int'])) $params['int'] = $loc->int;

	echo expCore::makeLink($params);
}

?>
