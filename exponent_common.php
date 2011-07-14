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

function __realpath($path) {
    $path = str_replace('\\','/',realpath($path));
    if ($path{1} == ':') {
        // We can't just check for C:/, because windows users may have the IIS webroot on X: or F:, etc.
        $path = substr($path,2);
    }
    return $path;
}

// Bootstrap, which will clean the _POST, _GET and _REQUEST arrays, and include 
// necessary setup files (exponent_setup.php, exponent_variables.php) as well as initialize
// the compatibility layer.
// This was moved into its own file from this file so that 'lighter' scripts could bootstrap.

include_once(dirname(__realpath(__FILE__)).'/exponent_bootstrap.php');
?>