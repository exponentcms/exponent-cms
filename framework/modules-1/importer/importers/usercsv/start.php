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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

$template = New template("importer", "_usercsv_form_start");

if (expFile::canCreate(BASE."framework/modules-1/importer/importers/usercsv/tmp/test") != SYS_FILES_SUCCESS) {
	$template->assign("error", "The modules/importer/importers/usercsv/tmp directory is not writable.  Please contact your administrator.");
	$template->output();
}else{

	//Setup the mete data (hidden values)
	$form = new form();
	$form->meta("module","importer");
	$form->meta("action","page");
	$form->meta("page","mapper");
	$form->meta("importer","usercsv");

	//Setup the arrays with the name/value pairs for the dropdown menus
	$delimiterArray = Array(
		','=>gt('Comma'),
		';'=>gt('Semicolon'),
		':'=>gt('Colon'),
		' '=>gt('Space'));

	//Register the dropdown menus
	$form->register("delimiter", gt('Delimiter Character'), New dropdowncontrol(",", $delimiterArray));
	$form->register("upload", gt('CSV File to Upload'), New uploadcontrol());
	$form->register("rowstart", gt('Row to Begin at'), New textcontrol("1",1,0,6));
	$form->register("submit", "", New buttongroupcontrol(gt('Next'),"", gt('Cancel')));
	$template->assign("form_html",$form->tohtml());
	$template->output();
}
?>
