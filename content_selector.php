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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','content_selector.php');

/* exdoc
 * Define Content Selector constant as 1, since we are not selecting orphaned content.
 * @node General
 */
define('CONTENT_SELECTOR',1);

// Initialize the Exponent Framework
include_once('exponent.php');
$section = $router->getSection();
$sectionObj = $router->getSectionObj($section);

// Call the real selector script.  It will use the value of CONTENT_SELECTOR to determine what it needs to do.
include_once('selector.php');

// Include the Selector script, which does all of the real work.
//include_once(dirname(__realpath(__FILE__)).'/selector.php');

?>