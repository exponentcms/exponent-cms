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
			$object->enable_categories = 0;
			$object->enable_feedback = 0;
			$object->enable_rss = false;
			$object->enable_tags = false;
			$object->collections = array();
			$object->aggregate = array();
      			$object->feed_title = "";
      			$object->feed_desc = "";
		} else {
			$form->meta('id',$object->id);

			$cols = unserialize($object->collections);
                        $object->collections = array();
                        $available_tags = array();

                        if (is_array($cols)) {
                                foreach ($cols as $col_id) {
                                        $collection = $db->selectObject('tag_collections', 'id='.$col_id);
					if (isset($collection) && $collection != null) {
                                        	$object->collections[$collection->id] = $collection->name;
					}

                                        //while we're here we will get he list of available tags.
                                        $tmp_tags = $db->selectObjects('tags', 'collection_id='.$col_id);
                                        foreach ($tmp_tags as $tag) {
                                                $available_tags[$tag->id] = $tag->name;
                                        }
                                }
                        }

                        //Get the tags the user chose to show in the group by views
                        $stags = unserialize($object->show_tags);
                        $object->show_tags = array();

                        if (is_array($stags)) {
                                foreach ($stags as $stag_id) {
                                        $show_tag = $db->selectObject('tags', 'id='.$stag_id);
                                        $object->show_tags[$show_tag->id] = $show_tag->name;
                                }
                        }
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
		//$form->register(null,'',new htmlcontrol('<h1>General Configuration</h1><hr size="1" />'));
		//$form->register('enable_categories',$i18n['enable_categories'],new checkboxcontrol($object->enable_categories,true));
		//$form->register('enable_feedback',$i18n['enable_feedback'],new checkboxcontrol($object->enable_feedback,true));				
		$form->register(null,'',new htmlcontrol('<h1>Merge Calendars</h1><hr size="1" />'));
        $form->register('aggregate','Pull Events from These Other Calendars',new listbuildercontrol($selected_calendars,$all_calendars));
		$form->register(null,'',new htmlcontrol('<h1>RSS Configuration</h1><hr size="1" />'));
		$form->register('enable_rss',$i18n['enable_rss'], new checkboxcontrol($object->enable_rss));
   		$form->register('feed_title',$i18n['feed_title'],new textcontrol($object->feed_title,35,false,75));
   		$form->register('feed_desc',$i18n['feed_desc'],new texteditorcontrol($object->feed_desc));
		$form->register('submit','',new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));

		//$form->register(null,'',new htmlcontrol('<h1>Tagging</h1><hr size="1" />'));
    	//$form->register('enable_tags',$i18n['enable_tags'], new checkboxcontrol($object->enable_tags));
    	//$form->register('collections',$i18n['tag_collections'],new listbuildercontrol($object->collections,@$tc_list));
        //$form->register('group_by_tags',$i18n['group_by_tags'], new checkboxcontrol($object->group_by_tags));
        //$form->register(null,'',new htmlcontrol($i18n['show_tags_desc']));
        //$form->register('show_tags','',new listbuildercontrol($object->show_tags,$available_tags));
		
		return $form;
	}
	
	function update($values,$object) {
		$object->enable_categories = (isset($values['enable_categories']) ? 1 : 0);
		$object->enable_feedback = (isset($values['enable_feedback']) ? 1 : 0);
		$object->enable_rss = (isset($values['enable_rss']) ? 1 : 0);
		$object->enable_tags = (isset($values['enable_tags']) ? 1 : 0);
	        $object->group_by_tags = (isset($values['group_by_tags']) ? 1 : 0);
        	$object->show_tags = serialize(listbuildercontrol::parseData($values,'show_tags'));
	        $object->collections = serialize(listbuildercontrol::parseData($values,'collections'));
	        $object->aggregate = serialize(listbuildercontrol::parseData($values,'aggregate'));
    		$object->feed_title = $values['feed_title'];
	    	$object->feed_desc = $values['feed_desc'];
		return $object;
	}
}

?>
