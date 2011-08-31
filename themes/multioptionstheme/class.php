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

if (class_exists('multioptionstheme')) return;

class multioptionstheme {
	function name() { return "Multi-Options Theme"; }
	function author() { return "David Leffler"; }
	function description() { return "A user configurable simple theme from <a href=\"http://andreasviklund.com/\" target=\"_blank\">andreasviklund.com"; }

	function configureTheme() {
	}
	
	function saveConfiguration() {
	}
}

?>