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

$f = $db->selectObject('formbuilder_form','id='.intval($_POST['form_id']));
if ($f) {
	if (expPermissions::check('editform',unserialize($f->location_data))) {
		$ctl = null;
		$control = null;
		if (isset($_POST['id'])) {
			$control = $db->selectObject('formbuilder_control','id='.intval($_POST['id']));
			if ($control) {
				$ctl = unserialize($control->data);
				$ctl->identifier = $control->name;
				$ctl->caption = $control->caption;
			}
		}

		if (call_user_func(array($_POST['control_type'],'useGeneric')) == true) { 	
			$ctl = call_user_func(array('genericcontrol','update'),$_POST,$ctl);
		} else {
			$ctl = call_user_func(array($_POST['control_type'],'update'),$_POST,$ctl);
		}
		
		//lets make sure the name submitted by the user is not a duplicate. if so we will fail back to the form
		$check = $db->selectObject('formbuilder_control', 'name="'.$ctl->identifier.'" AND form_id='.$f->id);
		if (!empty($check) && empty($_POST['id'])) {
			//expValidator::failAndReturnToForm(gt('A field with the same name already exists for this form'), $_POST);
			flash('error', gt('A field by the name")." "'.$ctl->identifier.'" ".gt("already exists on this form'));
			expHistory::returnTo('editable');
		}

		if ($ctl != null) {
			$name = substr(preg_replace('/[^A-Za-z0-9]/','_',$ctl->identifier),0,20);
			if (!isset($_POST['id']) && $db->countObjects('formbuilder_control',"name='".$name."' and form_id=".intval($_POST['form_id'])) > 0) {
				$post = $_POST;
				$post['_formError'] = gt('Identifier must be unique.');
				expSession::set('last_POST',$post);
			} 
			elseif ($name=='id' || $name=='ip' || $name=='user_id' || $name=='timestamp') {
				$post = $_POST;
				$post['_formError'] = sprintf(gt('Identifier cannot be "%s".'),$name);
				expSession::set('last_POST',$post);
			} else {
				if (!isset($_POST['id'])) {
					$control->name =  $name;
				}
				$control->caption = $ctl->caption;
				$control->form_id = intval($_POST['form_id']);
				$control->is_static = (isset($ctl->is_static)?$ctl->is_static:0);
				$control->data = serialize($ctl);
				
				if (isset($control->id)) {
					$db->updateObject($control,'formbuilder_control');
				} else {
					if (!$db->countObjects('formbuilder_control','form_id='.$control->form_id)) {
						$control->rank = 0;
					} else {
						$control->rank = $db->max('formbuilder_control','rank','form_id','form_id='.$control->form_id)+1;
					}
					$db->insertObject($control,'formbuilder_control');
					// reset summary report to all columns
					if (!$control->is_static) {
						$rpt = $db->selectObject('formbuilder_report','form_id='.$control->form_id);
						$rpt->column_names = "";
						$res = $db->updateObject($rpt,"formbuilder_report");
					}
				}
				
				formbuilder_form::updateTable($f);
			}
		}
		
		expHistory::returnTo('editable');
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>
