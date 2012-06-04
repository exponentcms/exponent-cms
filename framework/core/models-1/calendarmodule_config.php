<?php
##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

class calendarmodule_config {
	static function form($object) {
		global $db;
		$tag_collections = $db->selectObjects("tag_collections");
		foreach ($tag_collections as $key => $collection) {
			$tc_list[$collection->id] = $collection->name;
		}

		//eDebug($all_calendars);

		$form = new form();
        $form->is_tabbed = true;
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
//			$object->enable_rss = false;
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
		$calendars = expModules::listInstalledControllers('calendarmodule');
		$saved_aggregates = empty($object->aggregate) ? array() : unserialize($object->aggregate);
		$all_calendars = array();
		$selected_calendars = array();
		foreach ($calendars as $src => $cal) {
			$calendar_name = (empty($cal->title) ? 'Untitled' : $cal->title).' on page '.$cal->section;
			if ($src != $loc->src) {
				if (in_array($src, $saved_aggregates)) {
					$selected_calendars[$src] = $calendar_name;
				} else {
					$all_calendars[$src] =  $calendar_name;
				}
			}
		}
	    if (!isset($object->printlink)) {
            $object->printlink = false;
        }
		// setup the config form
		$form->register(null,'',new htmlcontrol('<h2>'.gt('General Configuration').'</h2>'),true,gt('Calendar'));
		// $form->register('enable_categories',gt('Enable Categories'),new checkboxcontrol($object->enable_categories,true));
        $form->register('printlink',gt('Display Printer-Friendly and Export-to-PDF Links'),new checkboxcontrol($object->printlink),true,gt('Calendar'));
		$form->register('enable_feedback',gt('Enable Feedback'),new checkboxcontrol($object->enable_feedback),true,gt('Calendar'));

		$form->register(null,'',new htmlcontrol('<h2>'.gt('Events Reminder Email').'</h2>'),true,gt('Reminders'));
        if (!empty($object->id)) {
            $form->register(null,'',new htmlcontrol('<h4>'.gt('sendreminders.php Calendar ID:').' '.$object->id.'</h4>'),true,gt('Reminders'));
        }

		// Get original style user lists
		// $selected_users = array();
		// foreach(unserialize($object->reminder_notify) as $i) {
			// $selected_users[$i] = $db->selectValue('user', 'firstname', 'id='.$i) . ' ' . $db->selectValue('user', 'lastname', 'id='.$i) . ' (' . $db->selectValue('user', 'username', 'id='.$i) . ')';
		// }
		// $userlist = array();
		// $list = user::getAllUsers();
		// foreach ($list as $i) {
			// if(!array_key_exists($i->id, $selected_users)) {
				// $userlist[$i->id] = $i->firstname . ' ' . $i->lastname . ' (' . $i->username . ')';
			// }
		// }		
		// $form->register('reminder_notify',gt('Who should be reminded of events?'),new listbuildercontrol($selected_users, $userlist));
		
		// Get User list
		$defaults = array();
		$userlist = array();
		$users = user::getAllUsers();
        if (!empty($object->id)) foreach ($db->selectObjects('calendar_reminder_address','calendar_id='.$object->id.' and user_id != 0') as $address) {
			$locuser =  user::getUserById($address->user_id);
			$defaults[$locuser->id] = $locuser->firstname . ' ' . $locuser->lastname . ' (' . $locuser->username . ')';
		} 
		foreach ($users as $locuser) {
			if(!array_key_exists($locuser->id, $defaults)) {
				$userlist[$locuser->id] = $locuser->firstname . ' ' . $locuser->lastname . ' (' . $locuser->username . ')';
			}
		}
		$form->register('users',gt('Users'),new listbuildercontrol($defaults,$userlist),true,gt('Reminders'));

		// Get Group list	
		$defaults = array();
		$grouplist = array();
		$groups = group::getAllGroups();
		if ($groups != null) {
            if (!empty($object->id)) foreach ($db->selectObjects('calendar_reminder_address','calendar_id='.$object->id.' and group_id != 0') as $address) {
				$group =  group::getGroupById($address->group_id);
				$defaults[$group->id] = $group->name;
			}
			foreach ($groups as $group) {
				if(!array_key_exists($group->id, $defaults)) {
					$grouplist[$group->id] = $group->name;
				}			
			}
			$form->register('groups',gt('Groups'),new listbuildercontrol($defaults,$grouplist),true,gt('Reminders'));
		}

		// Get Freeform list		
		$defaults = array();
        if (!empty($object->id)) foreach ($db->selectObjects('calendar_reminder_address','calendar_id='.$object->id." and email != ''") as $address) {
			$defaults[$address->email] = $address->email;
		}
		$form->register('addresses',gt('Other Addresses'),new listbuildercontrol($defaults,null),true,gt('Reminders'));
		
		$form->register('email_title_reminder',gt('Message Subject Prefix'),new textcontrol($object->email_title_reminder,45),true,gt('Reminders'));
		$form->register('email_from_reminder',gt('From (Display)'),new textcontrol($object->email_from_reminder,45),true,gt('Reminders'));
		$form->register('email_address_reminder',gt('From (Email Address)'),new textcontrol($object->email_address_reminder,45),true,gt('Reminders'));
		$form->register('email_reply_reminder',gt('Reply-to'),new textcontrol($object->email_reply_reminder,45),true,gt('Reminders'));
		$form->register('email_showdetail',gt('Show detail in message?'),new checkboxcontrol($object->email_showdetail),true,gt('Reminders'));
		$form->register('email_signature',gt('Email Signature'),new texteditorcontrol($object->email_signature,5,30),true,gt('Reminders'));

		$form->register(null,'',new htmlcontrol('<h2>'.gt('Aggregate Events').'</h2>'),true,gt('Aggregation'));
		$form->register('aggregate',gt('Aggregate events from similar modules'),new listbuildercontrol($selected_calendars,$all_calendars),true,gt('Aggregation'));
		// Get iCal list
		$defaults = array();
        if (!empty($object->id)) foreach ($db->selectObjects('calendar_external','calendar_id='.$object->id.' and type='.ICAL_TYPE) as $icaladdress) {
			$defaults[$icaladdress->url] = $icaladdress->url;
		}
		$form->register('ical_address',gt('Aggregate events from iCalendars').' (.ics)',new listbuildercontrol($defaults,null),true,gt('Aggregation'));
		// Get Google Calendar list
		$defaults = array();
        if (!empty($object->id)) foreach ($db->selectObjects('calendar_external','calendar_id='.$object->id.' and type='.GOOGLE_TYPE) as $googleaddress) {
			$defaults[$googleaddress->url] = $googleaddress->url;
		}
		$form->register('google_address',gt('Aggregate events from Google Calendars').' (.xml)',new listbuildercontrol($defaults,null),true,gt('Aggregation'));

		$form->register(null,'',new htmlcontrol('<h2>'.gt('iCalendar Configuration').'</h2>'),true,gt('iCalendar'));
//		$form->register('enable_rss',gt('Enable RSS'), new checkboxcontrol($object->enable_rss));
		$form->register('enable_ical',gt('Enable iCalendar'), new checkboxcontrol($object->enable_ical),true,gt('iCalendar'));
//   		$form->register('feed_title',gt('Title for this iCal feed'),new textcontrol($object->feed_title,35,false,75));
//   		$form->register('feed_desc',gt('Description for this iCal feed'),new texteditorcontrol($object->feed_desc));
		$form->register('rss_cachetime', gt('Recommended iCal feed update interval in minutes (1440 = 1 day)'), new textcontrol($object->rss_cachetime),true,gt('iCalendar'));
		$form->register('rss_limit', gt('Maximum days of iCal items to publish (0 = all)'), new textcontrol($object->rss_limit),true,gt('iCalendar'));

		// $form->register(null,'',new htmlcontrol('<h2>'.gt('Tagging').'</h2><hr size="1" />'));
		// $form->register('enable_tags',gt('Enable Tags'), new checkboxcontrol($object->enable_tags));
		// $form->register('collections',gt('Tag Collections'),new listbuildercontrol($object->collections,$tc_list));
        // $form->register('group_by_tags',gt('Filter events by tags'), new checkboxcontrol($object->group_by_tags));
        // $form->register(null,'',new htmlcontrol(gt('Tags to show')));
        // $form->register('show_tags','',new listbuildercontrol($object->show_tags,$available_tags));
		$form->register('submit','',new buttongroupcontrol(gt('Save Config'),'',gt('Cancel')),true,'base');
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

//		$object->enable_rss = (isset($values['enable_rss']) ? 1 : 0);
		$object->enable_ical = (isset($values['enable_ical']) ? 1 : 0);
//		$object->feed_title = $values['feed_title'];
//		$object->feed_desc = $values['feed_desc'];
		$object->rss_cachetime = $values['rss_cachetime'];
		$object->rss_limit = $values['rss_limit'];

        $object->printlink = $values['printlink'];

		// $object->enable_tags = (isset($values['enable_tags']) ? 1 : 0);
		// $object->collections = serialize(listbuildercontrol::parseData($values,'collections'));
		// $object->group_by_tags = (isset($values['group_by_tags']) ? 1 : 0);
		// $object->show_tags = serialize(listbuildercontrol::parseData($values,'show_tags'));

		//Deal with addresses by first deleting All addresses as we will be rebuilding it.
		$db->delete('calendar_reminder_address','calendar_id='.$object->id);
        $data = new stdClass();
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
        $caldata->calendar_id = $object->id;
        if(isset($values['ical_address'])){
            $caldata->type = ICAL_TYPE;
            foreach (listbuildercontrol::parseData($values,'ical_address') as $url) {
                $caldata->url = $url;
                $db->insertObject($caldata,'calendar_external');
            }
        }
        if(isset($values['google_address'])){
            $caldata->type = GOOGLE_TYPE;
            foreach (listbuildercontrol::parseData($values,'google_address') as $url) {
                $caldata->url = $url;
                $db->insertObject($caldata,'calendar_external');
            }
        }
		return $object;
	}
}

?>