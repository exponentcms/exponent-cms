<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

function smarty_function_comments($params,&$smarty) {
	$hideform = empty($params['hideform']) ? false : true;
	$hidecomments = empty($params['hidecomments']) ? false : true;
	$title = empty($params['title']) ? 'Comments' : $params['title'];
	$formtitle = empty($params['formtitle']) ? 'Leave a comment' : $params['formtitle'];
    
    /* The global constants can be overriden by passing appropriate params */
    $require_login = empty($params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $params['require_login'];
    $require_approval = empty($params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $params['require_approval'];
    $require_notification = empty($params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $params['require_notification'];
    $notification_email = empty($params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $params['notification_email'];
    
	renderAction(array('controller'=>'expComment', 
			'action'=>'getComments', 
			'content_id'=>$params['content_id'], 
			'content_type'=>$params['content_type'], 
			'hideform'=>$hideform, 
			'hidecomments'=>$hidecomments,
			'title'=>$title,
			'formtitle'=>$formtitle,
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email
	));
}
