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

$template = new template('importer','_files_uploadForm');

if (!defined('SYS_FORMS')) require_once(BASE.'subsystems/forms.php');
exponent_forms_initialize();

$i18n = exponent_lang_loadFile('modules/importer/importers/files/start.php');

$form = new form();
$form->meta('module','importer');
$form->meta('action','page');
$form->meta('importer','files');
$form->meta('page','process');
$form->register('file',$i18n['file'],new uploadcontrol());
$form->register('submit','',new buttongroupcontrol($i18n['restore']));

$template->assign('form_html',$form->toHTML());
$template->output();

?>