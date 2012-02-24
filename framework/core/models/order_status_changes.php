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
 * @package Core
 */
class order_status_changes extends expRecord {
    public $table = 'order_status_changes';
    public $validates = array(
        'presence_of'=>array(
            'from_status_id'=>array('message'=>'Cound not determine which status this order was being changed from.'),
            'to_status_id'=>array('message'=>'Cound not determine which status this order was being changed to.'),
        ));
        
}

?>