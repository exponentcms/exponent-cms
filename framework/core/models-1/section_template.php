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
		$i18n = exponent_lang_loadFile('datatypes/section_template.php');
	
//		if (!defined('SYS_FORMS')) require_once(BASE.'framework/core/subsystems-1/forms.php');
		require_once(BASE.'framework/core/subsystems-1/forms.php');
//		exponent_forms_initialize();
		
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
		$form->register('name',$i18n['name'],new textcontrol($object->name));
		
		if (!isset($object->id) && $object->parent != 0) { // Add the 'Add' drop down if not a top level
			global $db;
			$sections = $db->selectObjects('section_template','parent='.$object->parent);
			
			if (count($sections)) {
//				if (!defined('SYS_SORTING')) require_once(BASE.'framework/core/subsystems-1/sorting.php');
//				require_once(BASE.'framework/core/subsystems-1/sorting.php');
//				usort($sections,'exponent_sorting_byRankAscending');
				$sections = expSorter::sort(array('array'=>$sections,'sortby'=>'rank', 'order'=>'ASC'));

				$dd = array($i18n['position_top']);
				foreach ($sections as $s) $dd[] = sprintf($i18n['position_after'],$s->name);
				
				$form->register('rank',$i18n['rank'],new dropdowncontrol(count($dd)-1,$dd));
			} else $form->meta('rank',0);
		} else $form->meta('rank',0);
		
		if (is_readable(THEME_ABSOLUTE.'subthemes')) { // grab sub themes
			$form->register('subtheme',$i18n['subtheme'],new dropdowncontrol($object->subtheme,exponent_theme_getSubThemes()));
		}
		
		$form->register('active',$i18n['active'],new checkboxcontrol($object->active));
		$form->register('public',$i18n['public'],new checkboxcontrol($object->public));
		// Register the Page Meta Data controls.
		$form->register('page_title',$i18n['page_title'],new textcontrol($object->page_title));
		$form->register('keywords',$i18n['keywords'],new texteditorcontrol($object->keywords,5,25));
		$form->register('description',$i18n['description'],new texteditorcontrol($object->keywords,5,25));
		
		$form->register('submit','',new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
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