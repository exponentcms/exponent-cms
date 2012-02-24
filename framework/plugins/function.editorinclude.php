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
 * Smarty {editorinclude} function plugin
 *
 * Type:     function<br>
 * Name:     editorinclude<br>
 * Purpose:  include an external editor
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_editorinclude($params,&$smarty) {
	if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/editors/'.$params['filename'])) {
        echo ( URL_FULL.'themes/'.DISPLAY_THEME.'/editors/'.$params['filename'] );
    } elseif (file_exists(BASE.'external/editors/'.$params['filename'])) {
        echo ( URL_FULL.'external/editors/'.$params['filename'] );
    } else {
        echo ($params['filename']);
    }
}
