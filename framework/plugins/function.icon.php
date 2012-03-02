<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * Smarty {icon} function plugin
 *
 * Type:     function<br>
 * Name:     icon<br>
 * Purpose:  create an icon type link
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_icon($params,&$smarty) {
	$loc = $smarty->getTemplateVars('__loc');
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
	    if (expModules::controllerExists($params['module'])) {
	        $params['controller'] = expModules::getControllerName($params['module']);
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
    $config = $smarty->getTemplateVars('config');
    $noeditagg = 0;
    if (is_object($config)) {
        $noeditagg = !empty($config->noeditagg) ? $config->noeditagg : 0;
    } elseif (is_array($config)) {
        $noeditagg = !empty($config['noeditagg']) ? $config['noeditagg'] : 0;
    }
    if($noeditagg && ($smarty->getTemplateVars('__loc')->src != $params['src'])) return ;

	if (!isset($params['int'])) $params['int'] = $loc->int;

    // attempt to translate the alt, text, & title
    if (!empty($params['alt'])) {
        $params['alt'] = gt($params['alt']);
    }
    if (!empty($params['text'])) {
        $params['text'] = gt($params['text']);
    }
    if (!empty($params['title'])) {
        $params['title'] = gt($params['title']);
    }

	// figure out whether to use the edit icon or text, alt tags, etc.	
	$alt 	= (empty($params['alt'])) ? '' : $params['alt'];
	$class 	= (empty($params['class'])&&empty($params['img'])) ? $params['action'] : $params['class'];
	$text 	= (empty($params['text'])) ? '' : $params['text'];
	$title 	= (empty($params['title'])) ? (empty($text) ? ucfirst($class).' '.gt('this').' '.$smarty->getTemplateVars('modelname').' '.gt('item') : $text) : $params['title'];
	if (!empty($params['hash'])){
	    $hash = $params['hash'];
	    unset($params['hash']);
	}
	
	if (empty($params['img'])&&empty($params['text'])) {
    	$img 	= gt(ucfirst($class));
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
    unset($params['record']);
    $onclick = $params['onclick'];
    unset($params['onclick']);
	//eDebug($params);
	if (!empty($params['action'])) {
		echo '<a href="'.expCore::makeLink($params).'" title="'.$title.'" class="'.$class.'"';
		if ($params['action']=="delete" && empty($onclick))
            echo ' onclick="return confirm(\''.gt('Are you sure you want to delete this').' '.$smarty->getTemplateVars('modelname').' '.gt('item').'?\');"';
		if (!empty($onclick))
            echo ' onclick="'.$onclick.'"';
		echo '>'.$linktext.'</a>';
	} else {
		echo $linktext;
	}
}

?>
