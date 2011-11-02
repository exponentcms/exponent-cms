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

class section_template {
	static function form($object = null) {
		$form = new form();
		if (!isset($object->id)) {
			$object->name = '';
			$object->active = 1;
			$object->public = 1;
			$object->subtheme = '';
			$object->page_title = SITE_TITLE;
			$object->keywords = SITE_KEYWORDS;
			$object->description = SITE_DESCRIPTION;
			
			if (!isset($object->parent)) $object->parent = 0;
			// NOT IMPLEMENTED YET
			//$object->subtheme='';
		} else {
			$form->meta('id',$object->id);
		}
		$form->meta('parent',$object->parent);
		$form->register('name',gt('Name'),new textcontrol($object->name));
		
		if (!isset($object->id) && $object->parent != 0) { // Add the 'Add' drop down if not a top level
			global $db;
			$sections = $db->selectObjects('section_template','parent='.$object->parent);
			
			if (count($sections)) {
				$sections = expSorter::sort(array('array'=>$sections,'sortby'=>'rank', 'order'=>'ASC'));

				$dd = array(gt('At the Top'));
				foreach ($sections as $s) $dd[] = sprintf(gt('After')." %s",$s->name);
				
				$form->register('rank',gt('Position'),new dropdowncontrol(count($dd)-1,$dd));
			} else $form->meta('rank',0);
		} else $form->meta('rank',0);
		
		if (is_readable(THEME_ABSOLUTE.'subthemes')) { // grab sub themes
			$form->register('subtheme',gt('Theme Variation'),new dropdowncontrol($object->subtheme,expTheme::getSubThemes()));
		}
		
		$form->register('active',gt('Active'),new checkboxcontrol($object->active));
		$form->register('public',gt('Public'),new checkboxcontrol($object->public));
		// Register the Page Meta Data controls.
		$form->register('page_title',gt('Page Title'),new textcontrol($object->page_title));
		$form->register('keywords',gt('keywords'),new texteditorcontrol($object->keywords,5,25));
		$form->register('description',gt('Page Description'),new texteditorcontrol($object->keywords,5,25));
		
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		return $form;
	}
	
	static function update($values,$object=null) {
		$object->parent = $values['parent'];
		$object->name = $values['name'];
		$object->page_title = ($values['page_title'] != SITE_TITLE ? $values['page_title'] : "");
		$object->keywords = ($values['keywords'] != SITE_KEYWORDS ? $values['keywords'] : "");
		$object->description = ($values['description'] != SITE_DESCRIPTION ? $values['description'] : "");
		$object->active = (isset($values['active']) ? 1 : 0);
		$object->public = (isset($values['public']) ? 1 : 0);
		if (isset($values['subtheme'])) $object->subtheme = $values['subtheme'];
		if (isset($values['rank'])) $object->rank = $values['rank'];
		return $object;
	}
}

?>