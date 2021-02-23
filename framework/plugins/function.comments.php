<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * Smarty {comments} function plugin
 *
 * Type:     function<br>
 * Name:     comments<br>
 * Purpose:  Get comments
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_comments($params,&$smarty) {
    if (empty($params['record'])) return;  // no item to work with
//	$hideform = empty($params['hideform']) ? false : true;
//	$hidecomments = empty($params['hidecomments']) ? false : true;
    $config = $smarty->getTemplateVars('config');
    $hideform = !empty($config['usescomments']) ? true : (!empty($params['record']->disable_comments) ? true : false);  // we don't want new comments
   	$hidecomments = !empty($config['hidecomments']) ? true : (!empty($params['record']->disable_comments) ? true : false);  // we don't want to show comments
    if ($hideform && $hidecomments) return;  // we don't need to display anything
	$title = empty($params['title']) ? gt('Comments') : $params['title'];
	$formtitle = empty($params['formtitle']) ? gt('Leave a comment') : $params['formtitle'];
    $type = empty($params['type']) ? gt('Comment') : $params['type'];
    $ratings = !empty($params['ratings']) ? true : false;

    /* The global constants can be overridden by passing appropriate params */
    $require_login = empty($params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $params['require_login'];
    $require_approval = empty($params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $params['require_approval'];
    $require_notification = empty($params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $params['require_notification'];
    $notification_email = empty($params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $params['notification_email'];

	renderAction(array('controller'=>'expComment',
			'action'=>'showComments',
//			'content_id'=>$params['content_id'],
//			'content_type'=>$params['content_type'],
            'content_id'=>$params['record']->id,
            'content_type'=>$params['record']->classname,
            'config'=>$config,
			'hideform'=>$hideform,
			'hidecomments'=>$hidecomments,
			'title'=>$title,
			'formtitle'=>$formtitle,
            'type'=>$type,
            'ratings'=>$ratings,
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email
	));
}
