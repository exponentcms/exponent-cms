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

# doesn't this really depend on the tasks installed?

return array(
	's_backup'=>array(
		'name'=>'backup',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_datetime'=>array(
		'name'=>'datetime',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	's_smtp'=>array(
		'name'=>'smtp',
		'type'=>CORE_EXT_SUBSYSTEM,
		'comment'=>''
	),
	'm_importer'=>array(
		'name'=>'importer',
		'type'=>CORE_EXT_MODULE,
		'comment'=>''
	),
	'm_exporter'=>array(
		'name'=>'exporter',
		'type'=>CORE_EXT_MODULE,
		'comment'=>''
	)
);

?>