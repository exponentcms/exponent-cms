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

class formmodule {
	function name() { return 'Form'; }
    function displayname() { return gt('(old school) form'); } //for forwards compat with new modules
	function description() { return gt('Allows the creation of forms that can be emailed and/or stored in the database.'); }
	function author() { return 'OIC Group, Inc'; }
	
	function hasSources() { return true; }
	function hasContent() { return true; }
	function hasViews() { return true; }
	
	function supportsWorkflow() { return false; }
	
	function permissions($internal = "") {
		if ($internal == "") {
			return array(
				"manage"=>gt('Manage'),
				"editformsettings"=>gt('Configure'),
                "editform"=>gt('Edit Form'),
				"editreport"=>gt('Edit Report'),
				"viewdata"=>gt('View Posts'),
				"editdata"=>gt('Edit Posts'),
				"deletedata"=>gt('Delete Posts')
			);
		} else {
			return array(
				"manage"=>gt('Manage'),
                "editformsettings"=>gt('Configure'),
				"editform"=>gt('Edit Form'),
				"editreport"=>gt('Edit Report'),
				"viewdata"=>gt('View Posts'),
				"editdata"=>gt('Edit Posts'),
				"deletedata"=>gt('Delete Posts')
			);
		}
	}
	
	function show($view,$loc = null) {
		global $db;
        // require_once(BASE."framework/core/subsystems/forms/baseform.php");
        // require_once(BASE."framework/core/subsystems/forms/form.php");

		if (defined('PREVIEW_READONLY') && !defined('SELECTOR')) {
			// Pass
		}  else {
			$f = null;
			$f = $db->selectObject("formbuilder_form","location_data='".serialize($loc)."'");
			if (!$f) {
				//Create a form if it's missing...
				$f->name = "New Form";
				$f->description = "";
				$f->location_data = serialize($loc);
				$f->table_name = "";
				$f->is_email = 0;
				$f->is_saved = 0;
				$f->submitbtn = gt('Submit');
				$f->resetbtn = gt('Reset');
				$f->response = gt('Your form has been submitted');
				$f->subject = gt('Submitted form from site');
				$frmid = $db->insertObject($f,"formbuilder_form");
				//Create Default Report;
				$rpt->name = gt('Default Report');
				$rpt->description = "";
				$rpt->location_data = $f->location_data;
				$rpt->text = "";
				$rpt->column_names = "";
				$rpt->form_id = $frmid;
				$db->insertObject($rpt,"formbuilder_report");
				$f->id = $frmid;
			}

			$floc = unserialize($f->location_data);
			$controls = $db->selectObjects("formbuilder_control","form_id=".$f->id,'rank');
//			$controls = expSorter::sort(array('array'=>$controls,'sortby'=>'rank', 'order'=>'ASC'));

			$form = new form();
			$data = expSession::get('formmodule_data_'.$f->id);
			foreach ($controls as $c) {
				$ctl = unserialize($c->data);
				$ctl->_id = $c->id;
				$ctl->_readonly = $c->is_readonly;
				if(!empty($data[$c->name])) $ctl->default = $data[$c->name];
				$form->register($c->name,$c->caption,$ctl);
			}
			$form->register("submit","",new buttongroupcontrol($f->submitbtn,$f->resetbtn,""));
			
			//$form->meta("action","submit_form");
			$form->meta("action","confirm_form");
			$form->meta("m",$floc->mod);
			$form->meta("s",$floc->src);
			$form->meta("i",$floc->int);
			$form->meta("id",$f->id);
			$formmsg = '';
			$form->location(expCore::makeLocation("formbuilder",$floc->src,$floc->int));
			if (count($controls) == 0) {
				$form->controls['submit']->disabled = true;
				$formmsg .= gt('This form is blank. Select "Edit Form" to add input fields.').'<br>';
			}
			if ($f->is_saved == 0 && $f->is_email == 0) {
				$form->controls['submit']->disabled = true;
				$formmsg .= gt('There are no actions assigned to this form. Select "Edit Form Settings" then select "Email Form" and/or "Save to Database".');
			}
			$count = $db->countObjects("formbuilder_".$f->table_name);
			$template = new template("formmodule",$view,$loc);
			$template->assign("moduletitle",$f->name);
			$template->assign("description",$f->description);
			if ($formmsg) {
                flash('notice',$formmsg);
			}
			$template->assign("form_html",$form->toHTML($f->id));
			$template->assign("form",$f);
			$template->assign("count",$count);
			$template->register_permissions(array("manage","editform","editformsettings","editreport","viewdata","editdata","deletedata"),$loc);
			$template->output();
		}
	}
	
	function deleteIn($loc) {
		global $db;
		$form = $db->selectObject("formbuilder_form","location_data='".serialize($loc)."'");
		$db->delete("formbuilder_control","form_id=".$form->id);
		$db->delete("formbuilder_report","form_id=".$form->id);
		$db->delete("formbuilder_address","form_id=".$form->id);
		if ($form->is_saved == 1) {
			$db->dropTable("formbuilder_".$form->table_name);
		}
		$db->delete("formbuilder_form","location_data='".serialize($loc)."'");
	}
	
	function spiderContent($item = null) {
		// No content
		return false;
	}
}

?>