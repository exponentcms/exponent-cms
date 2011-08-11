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

if (!defined('EXPONENT')) exit('');

$ctl = new checkboxcontrol(false,true);
$ctl->disabled = 0;

return array(
    gt('Track Search Queries'),
    array(
        'SAVE_SEARCH_QUERIES'=>array(
            'title'=>gt('Save Search Queries'),
            'description'=>gt('It can be advantageous to know what your users are looking for. Take note that there is no user interface to see this data yet, you will need to query the database directly to see what your users are searching for.'),
            'control'=>$ctl
        )
    )
);

?>
