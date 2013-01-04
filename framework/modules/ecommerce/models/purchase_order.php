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
 * @package Core
 */
class purchase_order extends expRecord {
    public $table = 'purchase_order';
	public $has_one = array('vendor');
        
    public function __construct($params=null, $get_assoc=false, $get_attached=false) {
        parent::__construct($params, $get_assoc, $get_attached);
    }
    
}

?>