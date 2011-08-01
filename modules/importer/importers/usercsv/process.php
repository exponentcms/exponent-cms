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

//Sanity Check
if (!defined('EXPONENT')) exit('');
//if (!defined("SYS_FORMS")) require_once(BASE."subsystems/forms.php");
require_once(BASE."subsystems/forms.php");

//Create a new form object
//exponent_forms_initialize();

$i18n = exponent_lang_loadFile('modules/importer/importers/usercsv/process.php');

$form = new form();
$form->meta("module","importer");
$form->meta("action","page");
$form->meta("page","displayusers");
$form->meta("importer","usercsv");
$form->meta("column", $_POST["column"]);
$form->meta("delimiter", $_POST["delimiter"]);
$form->meta("filename", $_POST["filename"]);
$form->meta("rowstart", $_POST["rowstart"]);

if (in_array("username",$_POST["column"]) == false){
	$unameOptions = array(
               	"FILN"=>$i18n['filn'],
                "FILNNUM"=>$i18n['filn_num'],
       	        "EMAIL"=>$i18n['email'],
               	"FNLN"=>$i18n['fnln']);
}else{
	$unameOptions = array("INFILE"=>$i18n['in_file']);
}

if (in_array("password", $_POST["column"]) == false){
	$pwordOptions = array(
		"RAND"=>$i18n['rand_pass'],
		"DEFPASS"=>$i18n['def_pass']);
}else{
	$pwordOptions = array("INFILE"=>$i18n['pass_in_file']);
}

if (count($pwordOptions) == 1){
	$disabled = true;
}else{
	$disabled = false;
}

$form->register("unameOptions",$i18n['name_options'], New dropdowncontrol("INFILE", $unameOptions));
$form->register("pwordOptions", $i18n['pass_options'], New dropdowncontrol("defpass", $pwordOptions));
$form->register("pwordText", $i18n['password'], New textcontrol("", 10, $disabled));
$form->register("update", $i18n['update'], New checkboxcontrol(0, 0));
$form->register("submit", "", New buttongroupcontrol($i18n['submit'],"", $i18n['cancel']));
$template = New Template("importer", "_usercsv_form_geninfo");
$template->assign("form_html", $form->tohtml());
$template->output();

?>
