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

return array(
	gt('Minify Configuration'),
	array(
		'MINIFY_MAXAGE'=>array(
			'title'=>gt('Minify Max Age'),
			'description'=>gt('Maximum age of browser cache in seconds'),
			'control'=>new textcontrol()
		),
		'MINIFY_MAX_FILES'=>array(
			'title'=>gt('Minify Max Files'),
			'description'=>gt('Maximum # of files that can be specified in the "f" GET parameter'),
			'control'=>new textcontrol()
		),
		'MINIFY_URL_LENGTH'=>array(
			'title'=>gt('Minify URL Length'),
			'description'=>gt('The length of minification url'),
			'control'=>new textcontrol()
		),
		'MINIFY_ERROR_LOGGER'=>array(
			'title'=>gt('Minify Error Logger'),
			'description'=>gt('Set to true to log error messages to FirePHP'),
			'control'=>new checkboxcontrol()
		)
		
	)
);

?>