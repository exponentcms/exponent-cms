<?php
##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
/** @define "BASE" "." */

require_once('exponent.php');

// for backwards compatibility, use the new method
redirect_to(array('controller'=>'rss','action'=>'feed','module'=>expString::sanitize($_REQUEST['module']),'src'=>expString::sanitize($_REQUEST['src'])));

?>