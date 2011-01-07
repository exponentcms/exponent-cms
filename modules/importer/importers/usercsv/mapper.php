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

//Sanity check
if (!defined("EXPONENT")) exit("");
if (!defined("SYS_FORMS")) require_once(BASE."subsystems/forms.php");

$i18n = exponent_lang_loadFile('modules/importer/importers/usercsv/mapper.php');

//Get the post data for future massaging
$post = $_POST;

//Check to make sure the user filled out the required input.
if (!is_numeric($_POST["rowstart"])){
	unset($post['rowstart']);
	$post['_formError'] = $i18n['need_number'];
	exponent_sessions_set("last_POST",$post);
	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit('Redirecting...');
}

//Get the temp directory to put the uploaded file
$directory = "modules/importer/importers/usercsv/tmp";

//Get the file save it to the temp directory
if ($_FILES["upload"]["error"] == UPLOAD_ERR_OK) {
	$file = file::update("upload",$directory,null,time()."_".$_FILES['upload']['name']);
	if ($file == null) {
		switch($_FILES["upload"]["error"]) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$post['_formError'] = $i18n['err_file_toolarge'];
				break;
			case UPLOAD_ERR_PARTIAL:
				$post['_formError'] = $i18n['err_file_partial'];
				break;
			case UPLOAD_ERR_NO_FILE:
				$post['_formError'] = $i18n['err_file_none'];
				break;
			default:
				$post['_formError'] = $i18n['err_file_unknown'];
				break;
		}
		exponent_sessions_set("last_POST",$post);
		header("Location: " . $_SERVER['HTTP_REFERER']);
		exit("");
	}
}
/*
if (mime_content_type(BASE.$directory."/".$file->filename) != "text/plain"){
	$post['_formError'] = "File is not a delimited text file.";
	exponent_sessions_set("last_POST",$post);
	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit("");
}
*/

//split the line into its columns
$fh = fopen(BASE.$directory."/".$file->filename,"r");
for ($x=0; $x<$_POST["rowstart"]; $x++){
	$lineInfo = fgetcsv($fh, 2000, $_POST["delimiter"]);
}

$colNames = array(
	"none"=>$i18n['col_none'],
	"username"=>$i18n['col_username'],
	"password"=>$i18n['col_password'],
	"firstname"=>$i18n['col_firstname'],
	"lastname"=>$i18n['col_lastname'],
	"email"=>$i18n['col_email']
);

//Check to see if the line got split, otherwise throw an error
if ($lineInfo == null) {
	$post['_formError'] = sprintf($i18n['delimiter_error'], $_POST["delimiter"]); 
	exponent_sessions_set("last_POST",$post);
	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit("");
}else{
	//initialize the for stuff
	exponent_forms_initialize();
	//Setup the mete data (hidden values)
	$form = new form();
	$form->meta("module","importer");
	$form->meta("action","page");
	$form->meta("page","process");
	$form->meta("rowstart", $_POST["rowstart"]);
	$form->meta("importer","usercsv");
	$form->meta("filename",$directory."/".$file->filename);
	$form->meta("delimiter",$_POST["delimiter"]); 
	for ($i=0; $i< count($lineInfo); $i++) {
		$form->register("column[$i]", $lineInfo[$i], new dropdowncontrol("none", $colNames));
	}
	$form->register("submit", "", new buttongroupcontrol($i18n['submit'],"", $i18n['cancel']));
	$template = New Template("importer", "_usercsv_form_mapping");
	$template->assign("form_html", $form->tohtml());
	$template->output();
}

?>