<?php

##################################################
#
# Copyright (c) 2004-2019 OIC Group, Inc.
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
class model_alias extends expRecord {
	public $table = 'model_aliases';
	public $validates = array(
        'presence_of'=>array(
            'model'=>array('message'=>'Model / SKU is a required field.')
        ));

}

?>