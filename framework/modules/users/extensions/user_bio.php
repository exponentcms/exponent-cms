<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * @subpackage Profile-Extensions
 * @package Modules
 */

class user_bio extends expRecord {
#    public $validates = array(
#        'presence_of'=>array(
#            'title'=>array('message'=>'Title is a required field.'),
#            'body'=>array('message'=>'Body is a required field.'),
#        ));
        
    public function name() { return 'Biography'; }
	public function description() { return 'The extension allows users to enter biographical information about themselves.'; }
	
}

?>