<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class group extends expRecord {
    public $table = 'group';
    public $validates = array(
        'presence_of'=>array(
            'name'=>array('message'=>'Name is a required field.'),
        ),
        'uniqueness_of'=>array(
            'name'=>array('message'=>'There is already a group by that name.'),
        ),
        );
        
}

?>
