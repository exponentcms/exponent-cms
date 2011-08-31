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

if (class_exists('basetheme')) return;

class basetheme {
	function name() { return "Base Theme"; }
	function author() { return "Phillip Ball"; }
	function description() { return "A bare-bones theme based off <a href=\"http://developer.yahoo.com/yui/grids/\" target=\"_blank\">YUI 2 Gids</a>"; }
}

?>