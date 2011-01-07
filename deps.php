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

if (!defined('EXPONENT')) exit('');

return array(
	's_autoloader'=>array(
		'name'=>'autoloader',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_core'=>array(
		'name'=>'core',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_config'=>array(
		'name'=>'config',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_database'=>array(
		'name'=>'database',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_flow'=>array(
		'name'=>'flow',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_forms'=>array(
		'name'=>'forms',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_files'=>array(
		'name'=>'files',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_info'=>array(
		'name'=>'info',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_image'=>array(
		'name'=>'image',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_lang'=>array(
		'name'=>'lang',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_modules'=>array(
		'name'=>'modules',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_permissions'=>array(
		'name'=>'permissions',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_template'=>array(
		'name'=>'template',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_theme'=>array(
		'name'=>'theme',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_sessions'=>array(
		'name'=>'sessions',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_sorting'=>array(
		'name'=>'sorting',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_users'=>array(
		'name'=>'users',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_workflow'=>array(
		'name'=>'workflow',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	
	// Modules
	'm_administrationmodule'=>array(
		'name'=>'administrationmodule',
		'type'=>CORE_EXT_MODULE,
		'comment'=>''
	),
	'm_common'=>array(
		'name'=>'common',
		'type'=>CORE_EXT_MODULE,
		'comment'=>''
	),
	'm_workflow'=>array(
		'name'=>'workflow',
		'type'=>CORE_EXT_MODULE,
		'comment'=>''
	),
	'm_filemanager'=>array(
		'name'=>'filemanager',
		'type'=>CORE_EXT_MODULE,
		'comment'=>''
	),
	'm_info'=>array(
		'name'=>'info',
		'type'=>CORE_EXT_MODULE,
		'comment'=>''
	),
	
	't_portaltheme'=>array(
		'name'=>'portaltheme',
		'type'=>CORE_EXT_THEME,
		'comment'=>''
	)
);

?>
