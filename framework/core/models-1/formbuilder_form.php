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
/** @define "BASE" "../../.." */

class formbuilder_form {
	static function form($object) {
		global $db;
		//global $user;
		require_once(BASE.'framework/core/subsystems-1/forms.php');
		require_once(BASE.'framework/core/subsystems-1/users.php');

		$form = new form();
		if (!isset($object->id)) {
			$object->name = '';
			$object->description = '';
			$object->is_email = 0;
			$object->is_saved = 1;
			$object->response = gt('Your form has been submitted');
			$object->resetbtn = gt('Reset');
			$object->submitbtn = gt('Submit');
			$object->subject = gt('Submitted form from site');
		} else {
			$form->meta('id',$object->id);
		}
		
		$form->register('name',gt('Name'),new textcontrol($object->name));
		$form->register('description',gt('Description'),new texteditorcontrol($object->description));
		$form->register('response',gt('Response'),new htmleditorcontrol($object->response));

		$form->register(null,'', new htmlcontrol('<h3>'.gt('Button Settings').'</h3><hr size="1" />'));
		$form->register('submitbtn',gt('Submit Button Text'), new textcontrol($object->submitbtn));
		$form->register('resetbtn',gt('Reset Button Text'), new textcontrol($object->resetbtn));
		$form->register(null,'', new htmlcontrol('<h3>'.gt('Email Settings').'</h3><hr size="1" />'));
		$form->register('is_email',gt('Email Form'),new checkboxcontrol($object->is_email,false));
		
		// Get User list
    	$userlist = array();
    	$defaults = array();
		$users = user::getAllUsers();
		foreach ($db->selectObjects('formbuilder_address','form_id='.$object->id.' and user_id != 0') as $address) {
			$locuser =  exponent_users_getUserById($address->user_id);
			$defaults[$locuser->id] = $locuser->firstname . ' ' . $locuser->lastname . ' (' . $locuser->username . ')';
		}
		foreach ($users as $locuser) {
			if(!array_key_exists($locuser->id, $defaults)) {
				$userlist[$locuser->id] = $locuser->firstname . ' ' . $locuser->lastname . ' (' . $locuser->username . ')';
			}
		}
		$form->register('users',gt('Users'),new listbuildercontrol($defaults,$userlist));

		// Get Group list
		$grouplist = array();
		$defaults = array();
		$groups = user::getAllGroups();
		if ($groups != null) {
			foreach ($db->selectObjects('formbuilder_address','form_id='.$object->id.' and group_id != 0') as $address) {
				$group =  exponent_users_getGroupById($address->group_id);
				$defaults[$group->id] = $group->name;
			}
			foreach ($groups as $group) {
				if(!array_key_exists($group->id, $defaults)) {
					$grouplist[$group->id] = $group->name;
				}
			}
			$form->register('groups',gt('Groups'),new listbuildercontrol($defaults,$grouplist));
		}
		
		// Get free-form address list
		$defaults = array();
		foreach ($db->selectObjects('formbuilder_address','form_id='.$object->id." and email != ''") as $address) {
			$defaults[$address->email] = $address->email;
		}		
		$form->register('addresses',gt('Other Addresses'),new listbuildercontrol($defaults,null));
		
		$form->register('subject',gt('Email Subject'),new textcontrol($object->subject));
		$form->register(null,'', new htmlcontrol('<h3>'.gt('Database Settings').'</h3><hr size="1" /><br />'));
		$form->register('is_saved',gt('Save Submissions to the Database'),new checkboxcontrol($object->is_saved,false));
		$form->register(null,'', new htmlcontrol('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.gt('To help prevent data loss, you cannot remove a form\'s database table once it has been added.').'<br />'));
		if ($object->is_saved == 1) {
			$form->controls['is_saved']->disabled = true;
			$form->meta('is_saved','1');
		}
//		$form->register(null,'', new htmlcontrol('<br /><br /><br />'));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		
		return $form;
	}
	
	static function update($values,$object) {
		$object->name = $values['name'];
		$object->description = $values['description'];
		$object->is_email = (isset($values['is_email']) ? 1 : 0);
		$object->is_saved = (isset($values['is_saved']) ? 1 : 0);
		$object->response = $values['response'];
		$object->submitbtn = $values['submitbtn'];
		$object->resetbtn = $values['resetbtn'];
		$object->subject = $values['subject'];
		return $object;
	}
	
	static function updateTable($object) {
		global $db;
		
		require_once(BASE.'framework/core/subsystems-1/forms.php');
		if ($object->is_saved == 1) {
			$datadef =  array(
				'id'=>array(
					DB_FIELD_TYPE=>DB_DEF_ID,
					DB_PRIMARY=>true,
					DB_INCREMENT=>true),
				'ip'=>array(
					DB_FIELD_TYPE=>DB_DEF_STRING,
					DB_FIELD_LEN=>25),
				'timestamp'=>array(
					DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
				'user_id'=>array(
					DB_FIELD_TYPE=>DB_DEF_ID)
			);
			 
			if (!isset($object->id)) {
				$object->table_name = preg_replace('/[^A-Za-z0-9]/','_',$object->name);
				$tablename = 'formbuilder_'.$object->table_name;
				$index = '';
				while ($db->tableExists($tablename . $index)) {
					$index++;
				}
				$tablename = $tablename.$index;
				$db->createTable($tablename,$datadef,array());
				$object->table_name .= $index; 
			} else {
				if ($object->table_name == '') {
					$tablename = preg_replace('/[^A-Za-z0-9]/','_',$object->name);
					$index = '';
					while ($db->tableExists('formbuilder_' . $tablename . $index)) {
						$index++;
					}
					$object->table_name = $tablename . $index;
				}
				
				$tablename = 'formbuilder_'.$object->table_name;
				
				//If table is missing, create a new one.
				if (!$db->tableExists($tablename)) {
					$db->createTable($tablename,$datadef,array());
				}
			
				$ctl = null;
				$control_type = '';
				$tempdef = array();
				foreach ($db->selectObjects('formbuilder_control','form_id='.$object->id) as $control) {
					if ($control->is_readonly == 0) {
						$ctl = unserialize($control->data);
						$ctl->identifier = $control->name;
						$ctl->caption = $control->caption;
						$ctl->id = $control->id;
						$control_type = get_class($ctl);
						$def = call_user_func(array($control_type,'getFieldDefinition'));
						if ($def != null) {
							$tempdef[$ctl->identifier] = $def;
						}
					}
				}
				$datadef = array_merge($datadef,$tempdef);
				$db->alterTable($tablename,$datadef,array(),true);
			}
		}
		return $object->table_name;
	}
	
}

?>