<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * @subpackage Modifier
 */

/**
 * Smarty {signature} modifier plugin
 * Type:     modifier<br>
 * Name:     signature<br>
 * Purpose:  return the stored signature for a user id if available
 *
 * @param  integer $userid
 *
 * @return string
 */
function smarty_modifier_signature($userid) {
	global $db;

    $sig = $db->selectValue('user_signature','signature','user_id='.intval($userid));
    if (!empty($sig)) {
        $sig = '<h3>'.gt('About the author').'</h3>'.$sig;
    }
    $googleplus = $db->selectValue('user_signature','googleplus','user_id='.intval($userid));
    if (!empty($googleplus)) {
        if (empty($sig)) {
            $sig = user::getUserAttribution($userid) . ' ' . gt('is on');
        }
        $sig .= ' <a href="'.$googleplus.'?rel=author" title="'.user::getUserAttribution($userid).' '.gt('on Google+').'"><img src="'.PATH_RELATIVE.'framework/core/assets/images/gplus-16.png"></a>';
    }
    return $sig;
}

?>
