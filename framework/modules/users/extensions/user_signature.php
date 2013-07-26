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
 * @subpackage Profile-Extensions
 * @package Modules
 */

class user_signature extends expRecord {
#    public $validates = array(
#        'presence_of'=>array(
#            'title'=>array('message'=>'Title is a required field.'),
#            'body'=>array('message'=>'Body is a required field.'),
#        ));
        
    public function name() { return 'Signature'; }
	public function description() { return 'The extension allows users to enter a signature placed at the end of blog posts.'; }
	
}

?>