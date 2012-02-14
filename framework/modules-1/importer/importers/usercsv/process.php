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
               	"FILN"=>gt('First Initial / Last Name'),
                "FILNNUM"=>gt('First Initial / Last Name / Random Number'),
       	        "EMAIL"=>gt('Email Address'),
               	"FNLN"=>gt('First Name / Last Name'));
}else{
	$unameOptions = array("INFILE"=>gt('Username Specified in CSV File'));
}

if (in_array("password", $_POST["column"]) == false){
	$pwordOptions = array(
		"RAND"=>gt('Generate Random Passwords'),
		"DEFPASS"=>gt('Use the Default Password Supplied Below'));
}else{
	$pwordOptions = array("INFILE"=>gt('Password Specified in CSV File'));
}

if (count($pwordOptions) == 1){
	$disabled = true;
}else{
	$disabled = false;
}

$form->register("unameOptions",gt('User Name Generations Options'), New dropdowncontrol("INFILE", $unameOptions));
$form->register("pwordOptions", gt('Password Generation Options'), New dropdowncontrol("defpass", $pwordOptions));
$form->register("pwordText", gt('Default Password'), New textcontrol("", 10, $disabled));
$form->register("update", gt('Update users already in database'), New checkboxcontrol(0, 0));
$form->register("submit", "", New buttongroupcontrol(gt('Next'),"", gt('Cancel')));
$template = New Template("importer", "_usercsv_form_geninfo");
$template->assign("form_html", $form->tohtml());
$template->output();

?>
