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
	
if (!defined('EXPONENT')) exit('');

$i18n = exponent_lang_loadFile('modules/formbuilder/actions/edit_record.php');

if (!defined('SYS_FORMS')) include_once(BASE.'subsystems/forms.php');
exponent_forms_initialize();

// Sanitize required _GET parameters
$_GET['id'] = intval($_GET['id']);
$_GET['form_id'] = intval($_GET['form_id']);

$f = $db->selectObject('formbuilder_form','id='.$_GET['form_id']);
$data = $db->selectObject('formbuilder_'.$f->table_name,'id='.$_GET['id']);
$controls = $db->selectObjects('formbuilder_control','form_id='.$_GET['form_id']);

if ($f && $data && $controls) {
	if (exponent_permissions_check('editdata',unserialize($f->location_data))) {
		if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
		usort($controls,'exponent_sorting_byRankAscending');
		
		$form = new form();
		foreach ($controls as $c) {
			$ctl = unserialize($c->data);
			$ctl->_id = $c->id;
			$ctl->_readonly = $c->is_readonly;
			if ($c->is_readonly == 0) {
				$name = $c->name;
				if ($c->is_static == 0) {
					$ctl->default = $data->$name;
				}
      }
			$form->register($c->name,$c->caption,$ctl);
		}
		$form->register(uniqid(''),'', new htmlcontrol('<br /><br />'));
		$form->register('submit','',new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
		$form->meta('action','submit_form');
		$form->meta('m',$loc->mod);
		$form->meta('s',$loc->src);
		$form->meta('isedit',1);
		$form->meta('i',$loc->int);
		$form->meta('id',$f->id);
		$form->meta('data_id',$data->id);
		$form->location($loc);
		
		global $SYS_FLOW_REDIRECTIONPATH;
		$SYS_FLOW_REDIRECTIONPATH = "editfallback";
		$template = new template('formbuilder','_view_form');
		$template->assign('form_html',$form->toHTML($f->id));
		$template->assign('form',$f);
		$template->assign('edit_mode',1);
		$template->output();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>