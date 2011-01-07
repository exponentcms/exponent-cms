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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','source_selector.php');

define('SOURCE_SELECTOR',1);

// Initialize the Exponent Framework
include_once('exponent.php');
$section = $router->getSection();
$sectionObj = $router->getSectionObj($section);

// Call the real selector script.  It will use the value of SOURCE_SELECTOR to determine what it needs to do.
include_once('selector.php');

?>
