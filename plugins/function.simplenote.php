<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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
 * Smarty {simplenote} function plugin 
 * 
 * Type:     function<br>
 * Name:     simplenote<br>
 * Purpose:  Include the SimpleNote Attachable Item on the page.<br>
 * 
 * @example {simplenote content_type="product" content_id=$record->id}
 * 
 * @param array $params
 * @param mixed $smarty
 */
function smarty_function_simplenote($params,&$smarty) {    
    $hideform = empty($params['hideform']) ? false : true;
    $hidenotes = empty($params['hidenotes']) ? false : true;
    $title = empty($params['title']) ? 'Notes' : $params['title'];
    $formtitle = empty($params['formtitle']) ? 'Add a Note' : $params['formtitle'];
    
    $tab = empty($params['tab']) ? '' : $params['tab'];
    
    /* The global constants can be overriden by passing appropriate params */
    $require_login = empty($params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $params['require_login'];
    $require_approval = empty($params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $params['require_approval'];
    $require_notification = empty($params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $params['require_notification'];
    $notification_email = empty($params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $params['notification_email'];
    
    renderAction(array('controller'=>'expSimpleNote', 
            'action'=>'getNotes', 
            'content_id'=>$params['content_id'], 
            'content_type'=>$params['content_type'], 
            'hideform'=>$hideform, 
            'hidecomments'=>$hidenotes,
            'title'=>$title,
            'formtitle'=>$formtitle,
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email,
            'tab'=>$tab
    ));
}
