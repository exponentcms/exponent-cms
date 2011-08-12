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
	gt('HtmlToPdf Paths'),
	array(
		'HTMLTOPDF_PATH'=>array(
			'title'=>gt('HTML to PDF Path'),
			'description'=>gt('The actual path of the binary html to pdf library'),
			'control'=>new textcontrol()
		),
		'HTMLTOPDF_PATH_TMP'=>array(
			'title'=>gt('Html to PDF Tmp Directory'),
			'description'=>gt('The tmp directory to be used by htmltopdf library'),
			'control'=>new textcontrol()
		)
	)
);

?>