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

class calendarmodule_config {
	function form($object) {
		$i18n = exponent_lang_loadFile('datatypes/calendarmodule_config.php');
	
		if (!defined('SYS_FORMS')) require_once(BASE.'subsystems/forms.php');
		exponent_forms_initialize();
		
		global $db;
		$tag_collections = $db->selectObjects("tag_collections");
		foreach ($tag_collections as $tag_collections => $collection) {
			$tc_list[$collection->id] = $collection->name;
		}

		//eDebug($all_calendars);

		$form = new form();
		if (!isset($object->id)) {
			// $object->enable_categories = 0;
			$object->enable_feedback = 0;
			$object->reminder_notify = serialize(array());
			$object->email_title_reminder = "Calendar Reminder";
			$object->email_from_reminder = "Calendar Manager";
			$object->email_address_reminder = "calendar@".HOSTNAME;
			$object->email_reply_reminder = "calendar@".HOSTNAME;
			$object->email_showdetail = 0;
			$object->email_signature = "--\nThanks, Webmaster";			
			$object->aggregate = array();
			$object->enable_rss = false;
			$object->enable_ical = true;
			$object->feed_title = "";
			$object->feed_desc = "";
			$object->rss_limit = 365;
			$object->rss_cachetime = 60;
			// $object->enable_tags = false;
			// $object->collections = array();
			// $object->group_by_tags = false;
			// $object->show_tags = array();
		} else {
			$form->meta('id',$object->id);

			// $cols = unserialize($object->collections);
			// $object->collections = array();
			// $available_tags = array();
			// if (!empty($cols)) {
    			// foreach ($cols as $col_id) {
    				// $collection = $db->selectObject('tag_collections', 'id='.$col_id);
    				// $object->collections[$collection->id] = $collection->name;

    				// //while we're here we will get the list of available tags.
    				// $tmp_tags = $db->selectObjects('tags', 'collection_id='.$col_id);
    				// foreach ($tmp_tags as $tag) {
    					// $available_tags[$tag->id] = $tag->name;
    				// }
    			// }
			// }
			// //Get the tags the user chose to show in the group by views
			// $stags = unserialize($object->show_tags);
			// $object->show_tags = array();

// //			if (is_array($stags)) {
			// if (!empty($stags)) {
				// foreach ($stags as $stag_id) {
					// $show_tag = $db->selectObject('tags', 'id='.$stag_id);
					// $object->show_tags[$show_tag->id] = $show_tag->name;
				// }
			// }
		}

		// setup the listbuilder arrays for calendar aggregation.	
		$loc = unserialize($object->location_data);
		$calendars = exponent_modules_getModuleInstancesByType('calendarmodule');
		$saved_aggregates = empty($object->aggregate) ? array() : unserialize($object->aggregate);
		$all_calendars = array();
		$selected_calendars = array();
		foreach ($calendars as $src => $cal) {
			$calendar_name = (empty($cal[0]->title) ? 'Untitled' : $cal[0]->title).' on page '.$cal[0]->section;
			if ($src != $loc->src) {
				if (in_array($src, $saved_aggregates)) {
					$selected_calendars[$src] = $calendar_name;
				} else {
					$all_calendars[$src] =  $calendar_name;
				}
			}
		}
	
		// setup the config form	
		$form->register(null,'',new htmlcontrol('<h3>'.$i18n['general_conf'].'</h3><hr size="1" />'));
		// $form->register('enable_categories',$i18n['enable_categories'],new checkboxcontrol($object->enable_categories,true));
		$form->register('enable_feedback',$i18n['enable_feedback'],new checkboxcontrol($object->enable_feedback,true));				

		$form->register(null,'',new htmlcontrol('<h3>'.$i18n['events_reminder'].'</h3><hr size="1" />'));
		
		// Get original style user lists
		// $selected_users = array();
		// foreach(unserialize($object->reminder_notify) as $i) {
			// $selected_users[$i] = $db->selectValue('user', 'firstname', 'id='.$i) . ' ' . $db->selectValue('user', 'lastname', 'id='.$i) . ' (' . $db->selectValue('user', 'username', 'id='.$i) . ')';
		// }
		// $userlist = array();
		// $list = exponent_users_getAllUsers();
		// foreach ($list as $i) {
			// if(!array_key_exists($i->id, $selected_users)) {
				// $userlist[$i->id] = $i->firstname . ' ' . $i->lastname . ' (' . $i->username . ')';
			// }
		// }		
		// $form->register('reminder_notify',$i18n['reminder_notify'],new listbuildercontrol($selected_users, $userlist));
		
		// Get User list
		$defaults = array();
		$userlist = array();
		$users = exponent_users_getAllUsers();
		foreach ($db->selectObjects('calendar_reminder_address','calendar_id='.$object->id.' and user_id != 0') as $address) {
			$locuser =  exponent_users_getUserById($address->user_id);
			$defaults[$locuser->id] = $locuser->firstname . ' ' . $locuser->lastname . ' (' . $locuser->username . ')';
		} 
		foreach ($users as $locuser) {
			if(!array_key_exists($locuser->id, $defaults)) {
				$userlist[$locuser->id] = $locuser->firstname . ' ' . $locuser->lastname . ' (' . $locuser->username . ')';
			}
		}
		$form->register('users',$i18n['users'],new listbuildercontrol($defaults,$userlist));

		// Get Group list	
		$defaults = array();
		$grouplist = array();
		$groups = exponent_users_getAllGroups();
		if ($groups != null) {
			foreach ($db->selectObjects('calendar_reminder_address','calendar_id='.$object->id.' and group_id != 0') as $address) {
				$group =  exponent_users_getGroupById($address->group_id);
				$defaults[$group->id] = $group->name;
			}
			foreach ($groups as $group) {
				if(!array_key_exists($group->id, $defaults)) {
					$grouplist[$group->id] = $group->name;
				}			
			}
			$form->register('groups',$i18n['groups'],new listbuildercontrol($defaults,$grouplist));
		}

		// Get Freeform list		
		$defaults = array();
		foreach ($db->selectObjects('calendar_reminder_address','calendar_id='.$object->id." and email != ''") as $address) {
			$defaults[$address->email] = $address->email;
		}
		$form->register('addresses',$i18n['addresses'],new listbuildercontrol($defaults,null));
		
		$form->register('email_title_reminder',$i18n['message_subject_prefix'],new textcontrol($object->email_title_reminder,45));
		$form->register('email_from_reminder',$i18n['from_display'],new textcontrol($object->email_from_reminder,45));
		$form->register('email_address_reminder',$i18n['from_email'],new textcontrol($object->email_address_reminder,45));
		$form->register('email_reply_reminder',$i18n['reply_to'],new textcontrol($object->email_reply_reminder,45));
		$form->register('email_showdetail',$i18n['show_detail_in_message'],new checkboxcontrol($object->email_showdetail));
		$form->register('email_signature',$i18n['email_signature'],new texteditorcontrol($object->email_signature,5,30));

		$form->register(null,'',new htmlcontrol('<h3>'.$i18n['merge_calendars'].'</h3><hr size="1" />'));
		$form->register('aggregate',$i18n['pull_events'],new listbuildercontrol($selected_calendars,$all_calendars));

		$form->register(null,'',new htmlcontrol('<h3>'.$i18n['rss_configuration'].'</h3><hr size="1" />'));
		$form->register('enable_rss',$i18n['enable_rss'], new checkboxcontrol($object->enable_rss));
		$form->register('enable_ical',$i18n['enable_ical'], new checkboxcontrol($object->enable_ical));
   		$form->register('feed_title',$i18n['feed_title'],new textcontrol($object->feed_title,35,false,75));
   		$form->register('feed_desc',$i18n['feed_desc'],new texteditorcontrol($object->feed_desc));
		$form->register('rss_cachetime', $i18n['rss_cachetime'], new textcontrol($object->rss_cachetime));
		$form->register('rss_limit', $i18n['rss_limit'], new textcontrol($object->rss_limit));

		// $form->register(null,'',new htmlcontrol('<h3>'.$i18n['tagging'].'</h3><hr size="1" />'));
		// $form->register('enable_tags',$i18n['enable_tags'], new checkboxcontrol($object->enable_tags));
		// $form->register('collections',$i18n['tag_collections'],new listbuildercontrol($object->collections,$tc_list));
        // $form->register('group_by_tags',$i18n['group_by_tags'], new checkboxcontrol($object->group_by_tags));
        // $form->register(null,'',new htmlcontrol($i18n['show_tags_desc']));
        // $form->register('show_tags','',new listbuildercontrol($object->show_tags,$available_tags));

		$form->register('submit','',new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
		return $form;
	}
	
	function update($values,$object) {
		global $db;
		// $object->enable_categories = (isset($values['enable_categories']) ? 1 : 0);
		$object->enable_feedback = (isset($values['enable_feedback']) ? 1 : 0);
		
//		$object->reminder_notify = serialize(listbuildercontrol::parseData($values,'reminder_notify'));
		$object->email_title_reminder = $values['email_title_reminder'];
		$object->email_from_reminder = $values['email_from_reminder'];
		$object->email_address_reminder = $values['email_address_reminder'];
		$object->email_reply_reminder = $values['email_reply_reminder'];
		$object->email_showdetail = (isset($values['email_showdetail']) ? 1 : 0);	
		$object->email_signature = $values['email_signature'];
		
		$object->aggregate = serialize(listbuildercontrol::parseData($values,'aggregate'));

		$object->enable_rss = (isset($values['enable_rss']) ? 1 : 0);
		$object->enable_ical = (isset($values['enable_ical']) ? 1 : 0);
		$object->feed_title = $values['feed_title'];
		$object->feed_desc = $values['feed_desc'];
		$object->rss_cachetime = $values['rss_cachetime'];
		$object->rss_limit = $values['rss_limit'];
		
		// $object->enable_tags = (isset($values['enable_tags']) ? 1 : 0);
		// $object->collections = serialize(listbuildercontrol::parseData($values,'collections'));
		// $object->group_by_tags = (isset($values['group_by_tags']) ? 1 : 0);
		// $object->show_tags = serialize(listbuildercontrol::parseData($values,'show_tags'));

		//Deal with addresses by first deleting All addresses as we will be rebuilding it.
		$db->delete('calendar_reminder_address','calendar_id='.$object->id);
		$data->group_id = 0;
		$data->user_id = 0;
		$data->email='';
		$data->calendar_id = $object->id;
		if(isset($values['groups'])){
			foreach (listbuildercontrol::parseData($values,'groups') as $group_id) {
				$data->group_id = $group_id;
				$db->insertObject($data,'calendar_reminder_address');
			}
			$data->group_id = 0;
		}
		if(isset($values['users'])){
			foreach (listbuildercontrol::parseData($values,'users') as $user_id) {
				$data->user_id = $user_id;
				$db->insertObject($data,'calendar_reminder_address');
			}
			$data->user_id = 0;
		}
		if(isset($values['addresses'])){
			foreach (listbuildercontrol::parseData($values,'addresses') as $email) {
				$data->email = $email;
				$db->insertObject($data,'calendar_reminder_address');
			}
		}
		return $object;
	}
}

?>