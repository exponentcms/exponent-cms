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

if (class_exists('simpletheme')) return;

class simpletheme extends theme {
	public $user_configured = true;
    public $stock_theme = true;
	function name() { return "Simple Theme"; }
	function author() { return "Phillip Ball - Online Innovative Creations"; }
	function description() { return "A minimal, slick theme based on the <a href=\"http://yuilibrary.com/yui/docs/cssgrids/\" target=\"_blank\">YUI 3 Gridding System</a>"; }
}

?>