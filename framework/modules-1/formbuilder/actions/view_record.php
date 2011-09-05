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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');

include_once(BASE.'framework/core/subsystems-1/forms.php');
//include_once(BASE.'framework/core/subsystems-1/users.php');

// Sanitize required _GET variables.
$_GET['id'] = intval($_GET['id']);
$_GET['form_id'] = intval($_GET['form_id']);

$f = $db->selectObject('formbuilder_form','id='.$_GET['form_id']);
$controls = $db->selectObjects('formbuilder_control','form_id='.$f->id.' and is_readonly=0 and is_static = 0');
$data = $db->selectObject('formbuilder_'.$f->table_name,'id='.$_GET['id']);
$rpt = $db->selectObject('formbuilder_report','form_id='.$_GET['form_id']);

if ($f && $controls && $data && $rpt) {
	if (exponent_permissions_check('viewdata',unserialize($f->location_data))) {
		$controls = expSorter::sort(array('array'=>$controls,'sortby'=>'rank', 'order'=>'ASC'));
		
		$fields = array();
		$captions = array();
		foreach ($controls as $c) {
			$ctl = unserialize($c->data);
			$control_type = get_class($ctl);
			$name = $c->name;
			$fields[$name] = call_user_func(array($control_type,'templateFormat'),$data->$name,$ctl);
			$captions[$name] = $c->caption;
		}
		
		$captions['ip'] = gt('IP Address');
		$captions['timestamp'] = gt('Timestamp');
		$captions['user_id'] = gt('Username');
		$fields['ip'] = $data->ip;
		$locUser =  user::getUserById($data->user_id);
		$fields['user_id'] =  isset($locUser->username)?$locUser->username:'';
		$fields['timestamp'] = strftime(DISPLAY_DATETIME_FORMAT,$data->timestamp);
	
		if ($rpt->text == '') {
			$template = new template('formbuilder','_default_report');
		} else {
			$template = new template('formbuilder','_custom_report');
			$template->assign('template',$rpt->text);
		}
		$template->assign('title',$rpt->name);
		$template->assign('fields',$fields);
		$template->assign('captions',$captions);
		$template->assign('backlink',expHistory::getLastNotEditable());
		$template->assign('is_email',0);
		$template->output();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>