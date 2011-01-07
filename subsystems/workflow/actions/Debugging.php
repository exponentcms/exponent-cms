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

//GREP:HARDCODEDTEXT

if (!defined("EXPONENT")) exit("");

echo "<h3>Debugging Output:</h3>";

echo "Action Type: " . $action_type;

echo "<br /><hr size='1' />Policy Object:<br />";
echo "<xmp>";
print_r($policy);
echo "</xmp>";

echo "<br /><hr size='1' />Revision:<br />";
echo "<xmp>";
print_r($revision);
echo "</xmp>";

?>