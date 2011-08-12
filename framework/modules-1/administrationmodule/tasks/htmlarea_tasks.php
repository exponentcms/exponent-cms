<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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

$i18n = exponent_lang_loadFile('modules/administrationmodule/tasks/htmlarea_tasks.php');
//$editor_title = 'WYSIWYG Editor - ' . SITE_WYSIWYG_EDITOR;
$editor_title = 'WYSIWYG Editor';
return array(
	$editor_title=>array(
		'htmlarea_configs'=>array(
			'title'=>$i18n['toolbar_settings'],
			'module'=>'administrationmodule',
			'action'=>'htmlarea_configs',
			'icon'=>ICON_RELATIVE."admin/toolbar.png",
		),
                'icon'=>ICON_RELATIVE."admin/editor.png"
	)
)

?>
