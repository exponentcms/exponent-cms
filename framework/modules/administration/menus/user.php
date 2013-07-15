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

if (!defined('EXPONENT')) exit('');

global $user;

/////////////////////////////////////////////////////////////////////////
// FIGURE OUT IF WE"RE IN PREVIEW MODE OR NOT
/////////////////////////////////////////////////////////////////////////
$level = 99;
if (expSession::is_set('uilevel')) {
    $level = expSession::get('uilevel');
}

/////////////////////////////////////////////////////////////////////////
// BUILD THE MENU
/////////////////////////////////////////////////////////////////////////

$items = array(
    array(
        'text'      => gt("Edit My Profile"),
        'url'       => makeLink(array('controller' => 'users', 'action' => 'edituser', 'id' => $user->id)),
        'classname' => 'edit',
    ),
);

if ((!USER_NO_PASSWORD_CHANGE || $user->isAdmin()) && !$user->is_ldap) {
    $items[] = array(
        'text'      => gt("Change My Password"),
        'url'       => makeLink(array('controller' => 'users', 'action' => 'change_password')),
        'classname' => 'password',
    );
}

$items[] = array(
    'text'      => gt("Log Out"),
    'url'       => makeLink(array('controller' => 'login', 'action' => 'logout')),
    'classname' => 'logout',
);

if (!$user->isAdmin()) {
    $previewperms = !$db->selectValue('userpermission', 'uid', "uid='" . $user->id . "' AND (permission='manage' OR permission='edit')");
    if (!$previewperms) {
        $groups = $user->getGroupMemberships();
        foreach ($groups as $group) {
            if (!$previewperms) {
                $previewperms = !$db->selectValue('grouppermission', 'gid', "gid='" . $group->id . "' AND (permission='manage' OR permission='edit')");
            } else {
                break;
            }
        }
    }
} else $previewperms = true;

if ($previewperms) { // must be an admin user to use toggle_preview method
    $items[] = array(
        'text'      => ($level == UILEVEL_PREVIEW) ? gt('Turn Preview Mode off') : gt('Turn Preview Mode on'),
        'classname' => ($level == UILEVEL_PREVIEW) ? 'preview_on' : 'preview_off',
        'url'       => makeLink(array('controller' => 'administration', 'action' => 'toggle_preview'))
    );
}

return array(
    'text'      => $user->firstname . ' ' . $user->lastname,
    'classname' => 'quicklink user',
    'submenu'   => array(
        'id'       => 'events',
        'itemdata' => $items,
    )
);

//return array(
//    'text'=>$user->firstname.' '.$user->lastname,
//    'classname'=>'quicklink user',
//    'submenu'=>array(
//        'id'=>'user',
//        'itemdata'=>array(
//            array(
//                'text'=>gt("Edit My Profile"),
//                'url'=>makeLink(array('controller'=>'users','action'=>'edituser','id'=>$user->id)),
//                'classname'=>'edit',
//            ),
//            array(
//                'text'=>gt("Change My Password"),
//                'url'=>makeLink(array('controller'=>'users','action'=>'change_password')),
//                'classname'=>'password',
//            ),
//            array(
//                'text'=>gt("Log Out"),
//                'url'=>makeLink(array('controller'=>'login','action'=>'logout')),
//                'classname'=>'logout',
//            ),
//            array(
//                'text' => ($level == UILEVEL_PREVIEW)?gt('Turn Preview Mode off'):gt('Turn Preview Mode on'),
//                'classname' => ($level == UILEVEL_PREVIEW)?'preview_on':'preview_off',
//				'url' => makeLink(array('controller' => 'administration','action' => 'toggle_preview'))
//            ),
//        ),
//    )
//);

?>
