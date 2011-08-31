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
define('SCRIPT_FILENAME','orphan_content_selector.php');

// Define Content Selector constant as 2, since we are selecting orphaned content.
define('CONTENT_SELECTOR',2);

// Initialize the Exponent Framework
include_once('exponent.php');

if (!$user->isLoggedIn()) exit();

// Include the real selector script, which does all of the heavy lifting.
include_once('selector.php');

?>