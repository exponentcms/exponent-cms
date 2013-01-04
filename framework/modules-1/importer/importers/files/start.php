<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

$template = new template('importer','_files_uploadForm');

$form = new form();
$form->meta('module','importer');
$form->meta('action','page');
$form->meta('importer','files');
$form->meta('page','process');
$form->register('file',gt('Files Archive'),new uploadcontrol());
$form->register('submit','',new buttongroupcontrol(gt('Restore'),'','','uploadfile'));

$template->assign('form_html',$form->toHTML());
$template->output();

?>