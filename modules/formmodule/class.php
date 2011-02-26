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

class formmodule {
	function name() { return exponent_lang_loadKey('modules/formmodule/class.php','module_name'); }
	function description() { return exponent_lang_loadKey('modules/formmodule/class.php','module_description'); }
	function author() { return 'OIC Group, Inc'; }
	
	function hasSources() { return true; }
	function hasContent() { return true; }
	function hasViews() { return true; }
	
	function supportsWorkflow() { return false; }
	
	function permissions($internal = "") {
		$i18n = exponent_lang_loadFile('modules/formmodule/class.php');
		if ($internal == "") {
			return array(
				"administrate"=>$i18n['perm_administrate'],
				"editform"=>$i18n['perm_editform'],
				"editformsettings"=>$i18n['perm_editformsettings'],
				"editreport"=>$i18n['perm_editformreport'],
				"viewdata"=>$i18n['perm_viewdata'],
				"editdata"=>$i18n['perm_editdata'],
				"deletedata"=>$i18n['perm_deletedata']
			);
		} else {
			return array(
				"administrate"=>$i18n['perm_administrate'],
				"editform"=>$i18n['perm_editform'],
				"editformsettings"=>$i18n['perm_editformsettings'],
				"editreport"=>$i18n['perm_edit_formreport'],
				"viewdata"=>$i18n['perm_viewdata'],
				"editdata"=>$i18n['perm_editdata'],
				"deletedata"=>$i18n['perm_deletedata']
			);
		}
	}
	
	function show($view,$loc = null) {
		global $db;
		if (!defined("SYS_FORMS")) require_once(BASE."subsystems/forms.php");
		exponent_forms_initialize();
		
		$i18n = exponent_lang_loadFile('modules/formmodule/class.php');
		
		if (defined("PREVIEW_READONLY") && !defined("SELECTOR")) {
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
				$f->submitbtn = $i18n['default_submit'];
				$f->resetbtn = $i18n['default_reset'];
				$f->response = $i18n['default_response'];
				$f->subject = $i18n['default_subject'];
				$frmid = $db->insertObject($f,"formbuilder_form");
				//Create Default Report;
				$rpt->name = $i18n['default_report'];
				$rpt->description = "";
				$rpt->location_data = $f->location_data;
				$rpt->text = "";
				$rpt->column_names = "";
				$rpt->form_id = $frmid;
				$db->insertObject($rpt,"formbuilder_report");
				$f->id = $frmid;
			}
			global $SYS_FLOW_REDIRECTIONPATH;
			exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);
			$SYS_FLOW_REDIRECTIONPATH = "editfallback";
			exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);
			$SYS_FLOW_REDIRECTIONPATH = "exponent_default";
			
			$floc = unserialize($f->location_data);
			$controls = $db->selectObjects("formbuilder_control","form_id=".$f->id);
			if (!defined("SYS_SORTING")) require_once(BASE."subsystems/sorting.php");
			usort($controls,"exponent_sorting_byRankAscending");
			
			$form = new form();
			$data = exponent_sessions_get('formmodule_data_'.$f->id);
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
			$form->location(exponent_core_makeLocation("formbuilder",$floc->src,$floc->int));
			if (count($controls) == 0) {
				$form->controls['submit']->disabled = true;
				$formmsg .= $i18n['blank_form'].'<br>';
			}
			if ($f->is_saved == 0 && $f->is_email == 0) {
				$form->controls['submit']->disabled = true;
				$formmsg .= $i18n['no_actions']; 
			}
			$template = new template("formmodule",$view,$loc);
			$template->assign("moduletitle",$f->name);
			$template->assign("formmsg",$formmsg);
			$template->assign("form_html",$form->toHTML($f->id));
			$template->assign("form",$f);
			$template->register_permissions(array("administrate","editform","editformsettings","editreport","viewdata","editdata","deletedata"),$loc);
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
