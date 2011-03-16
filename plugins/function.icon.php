<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

function smarty_function_icon($params,&$smarty) {
	$loc = $smarty->_tpl_vars['__loc'];
	if (isset($params['record'])) { 
	    $record = $params['record'];
        $params['id'] = $record->id;
	};
    
    if ($record && empty($params['id'])){
        $params['id'] = $record->id;
    }
    
	// setup the link params
    if (!isset($params['controller']))
    {
	    if (!isset($params['module'])) $params['module'] = $loc->mod;
	    if (controllerExists($params['module'])) {
	        $params['controller'] = getControllerName($params['module']);
	        unset($params['module']);
	    }
    }
	// guess the src if it is not set
	if (!isset($params['src'])) {
	    if ($record) {
	        $modloc = expUnserialize($record->location_data);
			$params['src'] = $modloc->src;
	    } else if (!empty($params['controller']) || @call_user_func(array($loc->mod,'hasSources'))) {
			$params['src'] = $loc->src;
		}
	}
	
    if(!empty($smarty->_tpl_vars['config']['noeditagg']) && ($smarty->_tpl_vars['__loc']->src != $params['src'])) return ; 

	if (!isset($params['int'])) $params['int'] = $loc->int;


	// figure out whether to use the edit icon or text, alt tags, etc.	
	$alt 	= (empty($params['alt'])) ? '' : $params['alt'];
	$class 	= (empty($params['class'])&&empty($params['img'])) ? $params['action'] : $params['class'];
	$text 	= (empty($params['text'])) ? '' : $params['text'];
	$title 	= (empty($params['title'])) ? $text : $params['title'];
	if (!empty($params['hash'])){
	    $hash = $params['hash'];
	    unset($params['hash']);
	}
	
	if (empty($params['img'])&&empty($params['text'])) {
    	$img 	= $class;
	} else if (!empty($params['img'])) {
	    $img 	= '<img src="'.ICON_RELATIVE.$params['img'].'" title="'.$title.'" alt="'.$alt.'"'.XHTML_CLOSING.'>';
	}

	$linktext = $img.$text;

	// we need to unset these vars before we pass the params array off to makeLink
	unset($params['alt']);
	unset($params['title']);
	unset($params['text']);
	unset($params['img']);
	unset($params['class']);
	unset($params['record']);
	//eDebug($params);

	if (!empty($params['action'])) {
		echo '<a href="'.exponent_core_makeLink($params).'" title="'.$title.'" class="'.$class.'"';
		if (isset($params['onclick'])) echo ' onclick="'.$params['onclick'].'"';
		echo '>'.$linktext.'</a>';
	} else {
		echo $linktext;
	}
}

?>
