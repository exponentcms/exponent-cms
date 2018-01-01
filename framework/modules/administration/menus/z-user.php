<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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

if (!defined('EXPONENT'))
    exit('');

global $user, $db;

/////////////////////////////////////////////////////////////////////////
// FIGURE OUT IF WE"RE IN PREVIEW MODE OR NOT
/////////////////////////////////////////////////////////////////////////
$level = 99;
if (expSession::is_set('uilevel'))
    $level = expSession::get('uilevel');

/////////////////////////////////////////////////////////////////////////
// BUILD THE MENU
/////////////////////////////////////////////////////////////////////////

$items = array();
if (!$user->globalPerm('prevent_profile_change')) {
    $items[] = array(
        'text'      => gt("Edit My Profile"),
        'icon'      => 'fa-edit',
        'icon5'      => 'fas fa-edit',
        'classname' => 'edit',
        'url'       => makeLink(
            array(
                'controller' => 'users',
                'action' => 'edituser', 'id' => $user->id
            )
        ),
    );
}

if ((!USER_NO_PASSWORD_CHANGE || $user->isAdmin()) && !$user->is_ldap) {
    $items[] = array(
        'text'      => gt("Change My Password"),
        'icon'      => 'fa-lock',
        'icon5'      => 'fas fa-lock',
        'classname' => 'password',
        'url'       => makeLink(
            array(
                'controller' => 'users',
                'action' => 'change_password'
            )
        ),
    );
}

$items[] = array(
    'text'      => gt("Log Out"),
    'icon'      => 'fa-sign-out',
    'icon5'      => 'fas fa-sign-out-alt',
    'classname' => 'logout',
    'url'       => makeLink(
        array(
            'controller' => 'login',
            'action' => 'logout'
        )
    ),
);

if (!$user->isAdmin()) {
    $previewperms = !$db->selectValue(
        'userpermission',
        'uid',
        "uid='" . $user->id . "' AND (permission='manage' OR permission='edit')"
    );
    if (!$previewperms) {
        $groups = $user->getGroupMemberships();
        foreach ($groups as $group) {
            if (!$previewperms) {
                $previewperms = !$db->selectValue(
                    'grouppermission',
                    'gid',
                    "gid='" . $group->id . "' AND (permission='manage' OR permission='edit')"
                );
            } else {
                break;
            }
        }
    }
} else {
    $previewperms = true;
}

if ($previewperms) { // must be an admin user to use toggle_preview method
    $items[] = array(
        'text'      => ($level == UILEVEL_PREVIEW) ? gt('Turn Preview Mode off') : gt('Turn Preview Mode on'),
        'icon'      => ($level == UILEVEL_PREVIEW) ? 'fa-eye-slash text-success' : 'fa-eye',
        'icon5'      => ($level == UILEVEL_PREVIEW) ? 'fas fa-eye-slash text-success' : 'fas fa-eye',
        'classname' => ($level == UILEVEL_PREVIEW) ? 'preview_on' : 'preview_off',
        'url'       => makeLink(
            array(
                'controller' => 'administration',
                'action' => 'toggle_preview'
            )
        ),
    );
}

return array(
    'text'       => $user->firstname . ' ' . $user->lastname,
    'icon'       => 'fa-user',
    'icon5'       => 'fas fa-user',
    'classname'  => 'quicklink user',
    'alignright' => 1,
    'submenu'    => array(
        'id'       => 'events',
        'itemdata' => $items,
    )
);

?>
