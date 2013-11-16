<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

/**
 * @subpackage Models
 * @package Modules
 */

class media extends expRecord {
//    public $table = 'media';

    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile',
        'content_expTags'=>'expTag'
    );

    // public $validates = array(
    //     'presence_of'=>array(
    //         'title'=>array('message'=>'Title is a required field.')
    //     ));

}

?>