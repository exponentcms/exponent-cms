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

if (!defined("EXPONENT")) exit("");

if (!defined("SYS_FORMS")) require_once(BASE."subsystems/forms.php");
exponent_forms_initialize();

$f = $db->selectObject("formbuilder_form","id=".(isset($_REQUEST['form_id'])?intval($_REQUEST['form_id']):0));
if ($f) {
	if (exponent_permissions_check("editform",unserialize($f->location_data))) {
		if (isset($_POST['control_type']) && $_POST['control_type']{0} == ".") {
			$htmlctl = new htmlcontrol();
			$htmlctl->identifier = uniqid("");
			$htmlctl->caption = "";
			switch ($_POST['control_type']) {
				case ".break":
					$htmlctl->html = "<br />";
					break;
				case ".line":
					$htmlctl->html = "<hr size='1' />";
					break;
			}
			$ctl->name = uniqid("");
			$ctl->caption = "";
			$ctl->data = serialize($htmlctl);
			$ctl->form_id = $f->id;
			$ctl->is_readonly = 1;
			if (!$db->countObjects("formbuilder_control","form_id=".$f->id)) $ctl->rank = 0;
			else $ctl->rank = $db->max("formbuilder_control","rank","form_id","form_id=".$f->id)+1;
			$db->insertObject($ctl,"formbuilder_control");
			exponent_flow_redirect();
		} else {
			$control_type = "";
			$ctl = null;
			if (isset($_GET['id'])) {
				$control = $db->selectObject("formbuilder_control","id=".intval($_GET['id']));
				if ($control) {
					$ctl = unserialize($control->data);
					$ctl->identifier = $control->name;
					$ctl->caption = $control->caption;
					$ctl->id = $control->id;
					$control_type = get_class($ctl);
					$f->id = $control->form_id;
				}
			}
			if ($control_type == "") $control_type = $_POST['control_type'];
			$form = call_user_func(array($control_type,"form"),$ctl);
			$form->location($loc);
			if ($ctl) { 
				$form->controls['identifier']->disabled = true;
				$form->meta("id",$ctl->id);
				$form->meta("identifier",$ctl->identifier);
			}
			$form->meta("action","save_control");
			$form->meta('control_type',$control_type);
			$form->meta('form_id',$f->id);
			
			echo $form->toHTML();
		}
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>