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

return array(
    'text'=>$user->firstname.' '.$user->lastname,
    'classname'=>'quicklink user',
    'submenu'=>array(
        'id'=>'user',
        'itemdata'=>array(
            array(
                'text'=>gt("Edit My Profile"),
                'url'=>makeLink(array('controller'=>'users','action'=>'edituser','id'=>$user->id)),
                'classname'=>'edit',
            ),
            array(
                'text'=>gt("Change My Password"),
                'url'=>makeLink(array('controller'=>'users','action'=>'change_password')),
                'classname'=>'password',
            ),
            array(
                'text'=>gt("Log Out"),
                'url'=>makeLink(array('controller'=>'login','action'=>'logout')),
                'classname'=>'logout',
            ),
            array(
                'text' => ($level == UILEVEL_PREVIEW)?gt('Turn Preview Mode off'):gt('Turn Preview Mode on'),
                'classname' => ($level == UILEVEL_PREVIEW)?'preview_on':'preview_off',
				'url' => makeLink(array('controller' => 'administration','action' => 'toggle_preview'))
            ),
        ),
    )
);

?>
