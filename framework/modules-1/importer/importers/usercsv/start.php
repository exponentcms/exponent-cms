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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

//if (!defined("SYS_FORMS")) require_once(BASE."framework/core/subsystems-1/forms.php");
//if (!defined("SYS_FILES")) require_once(BASE."framework/core/subsystems-1/files.php");
require_once(BASE."framework/core/subsystems-1/forms.php");
require_once(BASE."framework/core/subsystems-1/files.php");

$template = New template("importer", "_usercsv_form_start");

if (expFile::canCreate(BASE."framework/modules-1/importer/importers/usercsv/tmp/test") != SYS_FILES_SUCCESS) {
	$template->assign("error", "The modules/importer/importers/usercsv/tmp directory is not writable.  Please contact your administrator.");
	$template->output();
}else{
	//initialize the for stuff
//	exponent_forms_initialize();
	
	$i18n = exponent_lang_loadFile('modules/importer/importers/usercsv/start.php');
	
	//Setup the mete data (hidden values)
	$form = new form();
	$form->meta("module","importer");
	$form->meta("action","page");
	$form->meta("page","mapper");
	$form->meta("importer","usercsv");

	//Setup the arrays with the name/value pairs for the dropdown menus
	$delimiterArray = Array(
		','=>$i18n['comma'],
		';'=>$i18n['semicolon'],
		':'=>$i18n['colon'],
		' '=>$i18n['space']);

	//Register the dropdown menus
	$form->register("delimiter", $i18n['delimiter'], New dropdowncontrol(",", $delimiterArray));
	$form->register("upload", $i18n['upload'], New uploadcontrol());
	$form->register("rowstart", $i18n['rowstart'], New textcontrol("1",1,0,6));
	$form->register("submit", "", New buttongroupcontrol($i18n['submit'],"", $i18n['cancel']));
	$template->assign("form_html",$form->tohtml());
	$template->output();
}
?>
