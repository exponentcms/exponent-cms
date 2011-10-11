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

$item = null;
$iloc = null;
if (isset($_GET['id'])) {
	$item = $db->selectObject('calendar','id=' . intval($_GET['id']));	
	if ($item) {
		if (isset($_GET['date_id'])) {
			$item->eventdate = $db->selectObject('eventdate','id='.intval($_GET['date_id']));
		} else {
			$item->eventdate = $db->selectObject('eventdate','event_id='.$item->id);
		}
		$item->eventstart += $item->eventdate->date;
		$item->eventend += $item->eventdate->date;
		$loc = unserialize($item->location_data);
		$iloc = expCore::makeLocation($loc->mod,$loc->src,$item->id);
	}
}

if (($item == null && expPermissions::check('post',$loc)) ||
	($item != null && expPermissions::check('edit',$loc)) ||
	($iloc != null && expPermissions::check('edit',$iloc))
) {
	$form = calendar::form($item);
	$form->meta('action','save');
	$form->location($loc);
	
	$config = $db->selectObject('calendarmodule_config',"location_data='".serialize($loc)."'");
	if (!$config) {
		$config->enable_categories = 0;
		$config->enable_feedback = 0;
	}
	
	// if (isset($config->enable_tags)) {
		// $cols = array();
		// $tags = array();
		// $cols = unserialize($config->collections);
		// if (count($cols) > 0) {
			// foreach ($cols as $col) {
				// $available_tags = array();
				// $available_tags = $db->selectObjects('tags', 'collection_id='.$col);
				// $tags = array_merge($tags, $available_tags);
			// }

			//$tags = expSorter::sort(array('array'=>$tags,'sortby'=>'name', 'order'=>'ASC', 'ignore_case'=>true));
			// $tag_list = array();
			// foreach ($tags as $tag) {
				// $tag_list[$tag->id] = $tag->name;
			// }

			// $selected_tags = array();
			// $used_tags = array();
			// if (isset($item->id)) {
				// $tag_ids = unserialize($item->tags);
// //				if (is_array($tag_ids) && count($tag_ids)>0) {  //If it's not an array, we don't have any tags.
				// if (!empty($tag_ids)) {  //If it's not an array, we don't have any tags.
					// $selected_tags = $db->selectObjectsInArray('tags', $tag_ids, 'name');
					// foreach ($selected_tags as $selected_tag) {
							// $used_tags[$selected_tag->id] = $selected_tag->name;
					// }
				// }
			// }

			// if (count($tag_list) > 0) {
				// $form->registerAfter('tag_header','tags',gt('Tags'),new listbuildercontrol($used_tags,$tag_list));
			// } else {
				// $form->registerAfter('tag_header','tags', '',new htmlcontrol('<br /><div>There are no tags assigned to the collection(s) available to this module.</div>'));
			// }
		// } else {
			// $form->registerAfter('tag_header','tags', '',new htmlcontrol('<br /><div>No tag collection have been assigned to this module</div>'));
		// }
	// }
	
	// if ($config->enable_categories == 1) {
		// $ddopts = array();
		// foreach ($db->selectObjects('category',"location_data='".serialize($loc)."' ORDER BY rank ASC") as $opt) {
			// $ddopts[$opt->id] = $opt->name;
		// }
// //		uasort($ddopts,'strnatcmp');

		// if (!isset($item->category_id)) $item->category_id = null;
		// $form->registerAfter('eventend','category',gt('Category'),new dropdowncontrol($item->category_id,$ddopts));
		// $form->registerBefore('category', null, '', new htmlcontrol('<hr size="1" />'));
	// }
	
	if ($config->enable_feedback == 1) {
		$form->registerBefore('submit', null,'', new htmlcontrol('<hr size="1" />'));
		$allforms = array();
		$allforms[''] = gt('Disallow Feedback');
		$allforms = array_merge($allforms, expTemplate::listFormTemplates("forms/email"));
		$feedback_form = ($item == null ? 0 : $item->feedback_form);
		$feedback_email = ($item == null ? '' : $item->feedback_email);
		$form->registerAfter('eventend', 'feedback_form', gt('Feedback Form'), new dropdowncontrol($feedback_form, $allforms));
		$form->registerAfter('feedback_form', 'feedback_email', gt('Feedback Email'), new textcontrol($feedback_email, 20));
		$form->registerBefore('feedback_form', null, '', new htmlcontrol('<hr size="1" />'));
	}

	if (isset($_GET['id']) && $_GET['id'] != 0) {
		$form->unregister('submit');
//		$buttons = "<div id=\"submitControl\" class=\"control buttongroup\"> ";
//		$buttons = "<input name=\"submitSubmit\" class=\"button\" type=\"submit\" value=\"Save\" onclick=\"if (checkRequired(this.form)) { if (validate(this.form)) { return true; } else { return false; } } else { return false; }\" /> ";
		$buttons = '<button name="submitSubmit" type="submit" id="'.$_GET['id'].'Submit" class="submit button awesome '.BTN_SIZE.' '.BTN_COLOR;
		$buttons .='" type="submit" value="' . "Save" . '"';
		$buttons .= ' onclick="if (checkRequired(this.form)) { if (validate(this.form)) { return true; } else { return false; } } else { return false; }"';
		$buttons .= ' />';
		$buttons .= "Save";
		$buttons .= ' </button>';

//		$buttons .= "<input name=\"submitNew\" class=\"button\" type=\"submit\" value=\"Save as New Event\" onclick=\"if (checkRequired(this.form)) { if (validate(this.form)) { return true; } else { return false; } } else { return false; }\" /> ";
		$buttons .= '<button name="submitNew" type="submit" id="'.$_GET['id'].'Submit" class="submit button awesome '.BTN_SIZE.' '.BTN_COLOR;
		$buttons .='" type="submit" value="' . "Save as New Event" . '"';
		$buttons .= ' onclick="if (checkRequired(this.form)) { if (validate(this.form)) { return true; } else { return false; } } else { return false; }"';
		$buttons .= ' />';
		$buttons .= "Save as New Event";
		$buttons .= ' </button>';

//		$buttons .= "<input class=\"button\" type=\"button\" value=\"Cancel\" onclick=\"document.location.href='".expHistory::getLastNotEditable()."'\" /> ";
		$buttons .= '<button type="cancel" class="cancel button awesome '.BTN_SIZE.' '.BTN_COLOR.'" onclick="document.location.href=\''.expHistory::getLastNotEditable().'\'; return false;"';
		$buttons .= '>';
		$buttons .= "Cancel";
		$buttons .= '</button>';
		
//		$buttons .= "</div>";
		$form->register(null,'',new htmlcontrol($buttons));
	}
	
	$form->validationScript = expModules::getJSValidationFile('calendarmodule','postedit');
	
	$template = new template('calendarmodule','_form_edit',$loc);
	$template->assign('form_html',$form->toHTML());
	$template->assign('is_edit',($item == null ? 0 : 1));
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
