<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

class faq extends expRecord {
    public $table = 'faqs';

    protected $attachable_item_types = array(
        'content_expCats'=>'expCat'
    );

    public $validates = array(
        'presence_of'=>array(
            'question'=>array('message'=>'Question is a required field.'),
            'submitter_name'=>array('message'=>'Name is a required field.'),
            'submitter_email'=>array('message'=>'Email is a required field.'),
        ),
        'is_valid_email'=>array(
            'submitter_email'=>array('message'=>'The email address you entered does not appear to be valid.'),
        )
   );
        
}

?>
