<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * Smarty {permissions} block plugin
 *
 * Type:     block<br>
 * Name:     permissions<br>
 * Purpose:  Set up a permissions block
 *
 * @param $params
 * @param $content
 * @param \Smarty $smarty
 * @param $repeat
 * @return string
 */
function smarty_block_permissions($params,$content,&$smarty, &$repeat) {
	if ($content) {
        global $user, $css_core;        
        if (empty($_GET['recymod'])) {
            $uilevel = 99;
            if (expSession::exists("uilevel")) $uilevel = expSession::get("uilevel");
            if (empty($css_core['admin-global'])) expCSS::pushToHead(array("corecss"=>"admin-global"));
            if ((isset($uilevel) && $uilevel == UILEVEL_PREVIEW) || !$user->isLoggedIn()) {
                $cntnt = "";
            } else {
                $cntnt = ((isset($uilevel) && $uilevel == UILEVEL_PREVIEW) || !$user->isLoggedIn()) ? "" : $content;
            }
    		return $cntnt;
        }
	}
}

?>