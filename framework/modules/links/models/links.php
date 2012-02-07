<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Phillip Ball
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

class links extends expRecord {

    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile',
        'content_expCats'=>'expCat'
    );

    public $validates = array(
        'presence_of'=>array(
            'title'=>array('message'=>'Title is a required field.'),
            'url'=>array('message'=>"you didn't provide a link...  Do so now."),
        )
    );

}

?>