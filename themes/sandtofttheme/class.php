<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

if (class_exists('sandtofttheme')) return;

class sandtofttheme extends theme {
	public $user_configured = true;
    public $stock_theme = true;
	function name() { return "Sandtoft Theme"; }
	function author() { return "Peter Short"; }
	function description() { return "Modified from Simple Theme"; }
}

?>