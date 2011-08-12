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

$i18n = exponent_lang_loadFile('modules/formbuilder/actions/view_form.php');

//if (!defined("SYS_FORMS")) require_once(BASE."framework/core/subsystems-1/forms.php");
require_once(BASE."framework/core/subsystems-1/forms.php");
//exponent_forms_initialize();

$f = null;
if (isset($_GET['id'])) {
	$f = $db->selectObject("formbuilder_form","id=".intval($_GET['id']));
}

if ($f) {
	if (exponent_permissions_check("editform",unserialize($f->location_data))) {
		expHistory::set('editable', $_GET);
		$loc = unserialize($f->location_data);
		$controls = $db->selectObjects("formbuilder_control","form_id=".$f->id);
//		if (!defined("SYS_SORTING")) require_once(BASE."framework/core/subsystems-1/sorting.php");
//		require_once(BASE."framework/core/subsystems-1/sorting.php");
//		usort($controls,"exponent_sorting_byRankAscending");
		$controls = expSorter::sort(array('array'=>$controls,'sortby'=>'rank', 'order'=>'ASC'));

		$form = new fakeform();
		foreach ($controls as $c) {
			$ctl = unserialize($c->data);
			$ctl->_id = $c->id;
			$ctl->_readonly = $c->is_readonly;
			$ctl->_controltype = get_class($ctl);
			$form->register($c->name,$c->caption,$ctl);
		}
		
		$template = new template("formbuilder","_view_form");
		$template->assign("form_html",$form->toHTML($f->id));
		$template->assign("form",$f);
		global $SYS_FLOW_REDIRECTIONPATH;
		$SYS_FLOW_REDIRECTIONPATH = "editfallback";
		$template->assign("backlink",expHistory::getLastNotEditable());
		$SYS_FLOW_REDIRECTIONPATH = "exponent_default";
		
		$types = exponent_forms_listControlTypes();
		$types[".break"] = $i18n['spacer'];
		$types[".line"] = $i18n['line'];
		uasort($types,"strnatcmp");
		array_unshift($types,$i18n['select']);
		$template->assign("types",$types);
		$template->assign("pickerurl",URL_FULL."source_selector.php?showmodules=formmodule&dest='+escape(\"".PATH_RELATIVE."?module=formbuilder&action=picked_source&form_id=".$f->id."&s=".$loc->src."&m=".$loc->mod ."\")+'&vmod=containermodule&vview=_sourcePicker");
		$template->output();
	} else {
		echo SITE_403_HTML;	
	}
} else {
	echo SITE_404_HTML;
}

?>
