<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * @param array $params based on expJavascript::panel()
 *          'id' to differentiate popups
 *          'width' width of popup, defaults to '300px'
 *          'type' id type of popup, defaults to 'info', also 'error' & 'alert'
 *          'buttons' text string of 2 button names separated by ':'
 *          'title' title of popup
 *          'close' should popup have a close button (x), defaults to true
 *          'trigger' what object to base event trigger on, defaults to 'selfpop' which displays when popup is ready
 *          'on' what 'event' to display popup, defaults to 'load', or 'click' if 'trigger' is set
 *          'onnogo' what url to browse to when the 'no' button is selected
 *          'onyesgo' what url to browse to when the 'yes' button is selected
 *          'fade' seconds duration of popup 'fade' in/out, defaults to false
 *          'modal' should the popup be 'modal', defaults to true
 *          'draggable' should the popup be 'draggable', defaults to false
 *          'fixedcenter' should the popup be centered, defaults to true
 *          'renderto' where to draw the popup, defaults to 'document.body'
 *          'constraintoviewport' should popup be constrained to the viewport, defaults to true
 *          'zindex' depth of popup, defaults to '50'
 *
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 */
function smarty_block_pop($params,$content,&$smarty, &$repeat) {
	if($content){
		$params['content'] = $content;
        if (empty($params['trigger']) && !empty($params['text'])) {
            $params['trigger'] = $params['id'];
            echo '<a href="#" id="' . $params['id'] . '">' . $params['text'] . '</a>';
        }
        if (isset($params['type'])) {
            if ($params['type'] == 'warning') {
                $params['type'] = 'alert';
            } elseif ($params['type'] == 'danger') {
                $params['type'] = 'error';
            }
        }
        if (isset($params['title'])) {
            $params['header'] = $params['title'];
            unset($params['title']);
        }
        if (isset($params['buttons'])) {
            $params['dialog'] = $params['buttons'];
            unset($params['buttons']);
        }
        $params['fade'] = 0.25;
		expJavascript::panel($params);
	}
}

?>

