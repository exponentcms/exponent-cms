<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
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
 * @subpackage Block
 */

/**
 * Smarty {pop} block plugin
 *
 * Type:     block<br>
 * Name:     pop<br>
 * Purpose:  Set up a pop block
 *
 * @param array $params
 *          'id' to differentiate popups
 *          'width' width of popup, defaults to '300px'
 *          'type' id type of popup, defaults to 'into'
 *          'dialog' text string of 2 button names separated by ':'
 *          'header' title of popup
 *          'renderto' where to draw the popup, defaults to 'document.body'
 *          'on' what 'event' to display popup, defaults to 'load'
 *          'trigger' what object to base event trigger on, defaults to 'selfpop' which displays when popup is ready
 *          'onnogo' what url to browse to when the 'no' button is selected
 *          'onyesgo' what url to browse to when the 'yes' button is selected
 *          'zindex' depth of popup, defaults to '50'
 *          'fixedcenter' should the popup be centered, defaults to true
 *          'fade' should popup 'fade' in/out, defaults to false
 *          'modal' should the popup be 'modal', defaults to true
 *          'draggable' should the popup be 'draggable', defaults to false
 *          'constraintoviewport' should popup be constrained to the viewport, defaults to true
 *          'close' should popup have a close button (x), defaults to true
 *
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 */  //NOTE: Deprecated due to expJavascript::panel() (yui2) use?  replace w/ modal?
function smarty_block_pop($params,$content,&$smarty, &$repeat) {
	if($content){
		$params['content'] = $content;
		expJavascript::panel($params);
	}
}

?>

