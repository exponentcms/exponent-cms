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

// Part of the Extensions category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('extensions',exponent_core_makeLocation('administrationmodule'))) {
//	if (!defined('SYS_FORMS')) require_once(BASE.'framework/core/subsystems-1/forms.php');
	require_once(BASE.'framework/core/subsystems-1/forms.php');
//	exponent_forms_initialize();
	$form = new form();
	
	$i18n = exponent_lang_loadFile('modules/administrationmodule/actions/upload_extension.php');
	
	$form->register(null,'',new htmlcontrol(exponent_core_maxUploadSizeMessage()));
	$form->register('mod_archive',$i18n['mod_archive'],new uploadcontrol());
	$form->register('submit','',new buttongroupcontrol($i18n['install']));
	$form->meta('module','administrationmodule');
	$form->meta('action','install_extension');

	$template = new template('administrationmodule','_form_uploadExt',$loc);
	$template->assign('form_html',$form->toHTML());
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>