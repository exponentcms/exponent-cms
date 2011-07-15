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


$i18n = exponent_lang_loadFile('conf/extensions/htmltopdf.structure.php');

return array(
	$i18n['title'],
	array(
		'HTMLTOPDF_PATH'=>array(
			'title'=>$i18n['htmltopdf_path'],
			'description'=>$i18n['htmltopdf_path_desc'],
			'control'=>new textcontrol()
		),
		'HTMLTOPDF_PATH_TMP'=>array(
			'title'=>$i18n['htmltopdf_tmp'],
			'description'=>$i18n['htmltopdf_tmp_desc'],
			'control'=>new textcontrol()
		)
	)
);

?>