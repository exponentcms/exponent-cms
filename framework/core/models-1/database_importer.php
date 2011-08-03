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
/** @define "BASE" "../../.." */

class database_importer {
	function form() {
		
		exponent_lang_loadDictionary('modules','database');
	
//		if (!defined('SYS_FORMS')) require_once(BASE.'framework/core/subsystems-1/forms.php');
		require_once(BASE.'framework/core/subsystems-1/forms.php');
//		exponent_forms_initialize();

		$form = new form();
		//Form is created to collect information from the user
		//Values set previously (defaults or user-entered) are displayed
		$form->register('dbengine',TR_DATABASE_DBTYPE,new dropdowncontrol('',exponent_database_backends()));
		$form->register('host',TR_DATABASE_HOST,new textcontrol(DB_HOST));
		$form->register('port',TR_DATABASE_PORT,new textcontrol(DB_PORT));
		$form->register('dbname',TR_DATABASE_DBNAME,new textcontrol(''));
		$form->register('username',TR_DATABASE_USER,new textcontrol(DB_USER));
		$form->register('pwd',TR_DATABASE_PWD,new passwordcontrol(''));
		
		return $form;
	}
}
?>
