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

$dh = opendir(BASE.'compat');
while (($file = readdir($dh)) !== false) {
	if (is_file(BASE.'compat/'.$file) && substr($file,-4,4) == '.php') {
		// Include each file in compat/, each of which is a function redefinition for older version of PHP
		include_once(BASE.'compat/'.$file);
	}
}

?>
