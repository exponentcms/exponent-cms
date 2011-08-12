<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');
global $user;

/////////////////////////////////////////////////////////////////////////
// FIGURE OUT IF WE"RE IN PREVIEW MODE OR NOT
/////////////////////////////////////////////////////////////////////////
$level = 99;
if (expSession::is_set('uilevel')) {
	$level = expSession::get('uilevel');
}	

if ($level == UILEVEL_PREVIEW) {
    $preview_url = makeLink(array('module'=>'previewmodule','action'=>'normal'));
    $preview_class = 'preview_on';
} else {
    $preview_url = makeLink(array('module'=>'previewmodule','action'=>'preview'));
    $preview_class = 'preview_off';
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
                'url'=>makeLink(array('controller'=>'users','action'=>'change_password','ud'=>$user->id)),
                'classname'=>'password',
            ),
            array(
                'text'=>gt("Log Out"),
                'url'=>makeLink(array('module'=>'loginmodule','action'=>'logout')),
                'classname'=>'logout',
            ),
            array(
                'text'=>gt('Preview Mode'),
                'classname'=>$preview_class,
                'url'=>$preview_url
            ),
        ),
    )
);

?>
