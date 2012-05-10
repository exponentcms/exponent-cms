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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');

$item = null;
$iloc = new stdClass();
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

if (($item == null && expPermissions::check('create',$loc)) ||
	($item != null && expPermissions::check('edit',$loc)) ||
	($iloc != null && expPermissions::check('edit',$iloc))
) {
	$form = calendar::form($item);
	$form->meta('action','save');
	$form->location($loc);
	
	$config = $db->selectObject('calendarmodule_config',"location_data='".serialize($loc)."'");
	if (!$config) {
//		$config->enable_categories = 0;
		$config->enable_feedback = 0;
	}
	
    $submit = $form->controls['submit'];
    $form->unregister('submit');
	if ($config->enable_feedback == 1) {
		$allforms = array();
		$allforms[''] = gt('Disallow Feedback');
//		$allforms = array_merge($allforms, expTemplate::listFormTemplates("forms/calendar"));
		$allforms = array_merge($allforms, expCore::buildNameList("forms", "forms/calendar", "tpl", "[!_]*"));
		$feedback_form = ($item == null ? 0 : $item->feedback_form);
		$feedback_email = ($item == null ? '' : $item->feedback_email);
		$form->register('feedback_form', gt('Feedback Form'), new dropdowncontrol($feedback_form, $allforms),true,gt('Feedback'));
		$form->register('feedback_email', gt('Feedback Email'), new textcontrol($feedback_email, 20),true,gt('Feedback'));
	}

    $form->register(null,null,new htmlcontrol('<div class="loadingdiv">'.gt('Loading Event').'</div>'),true,'base');
	if (isset($_GET['id']) && $_GET['id'] != 0) {
//		$form->unregister('submit');
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
		$form->register(null,'',new htmlcontrol($buttons),true,'base');
	} else {
        $form->register('submit','',$submit,true,'base');
    }
	
	$form->validationScript = PATH_RELATIVE.'framework/modules-1/calendarmodule/assets/js/postedit.validate.js';  //FIXME This is not working

	$template = new template('calendarmodule','_form_edit',$loc);
	$template->assign('form_html',$form->toHTML());
	$template->assign('is_edit',($item == null ? 0 : 1));
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
