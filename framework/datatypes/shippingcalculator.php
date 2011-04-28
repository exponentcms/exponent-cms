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

class shippingcalculator extends expRecord {
    public $table = 'shippingcalculator';
    public $icon = '';
    public $configdata = array();
    
    public function __construct($params=null, $get_assoc=true, $get_attached=true) {        
        parent::__construct($params, $get_assoc, $get_attached);
        
        // grab the config data for this calculator
        $this->configdata = empty($this->config) ? array() : unserialize($this->config);
        
        if (file_exists(BASE.'framework/modules/ecommerce/shippingcalculators/icons/'.$this->classname.'.gif')) {
            $this->icon = PATH_RELATIVE.'framework/modules/ecommerce/shippingcalculators/icons/'.$this->classname.'.gif';
        } else {
            $this->icon = PATH_RELATIVE.'framework/modules/ecommerce/shippingcalculators/icons/default.gif';
        }
        
    }

    public function meetsCriteria() {
        return true;
    }
    
    public function getHandling(){
        return 0;
    }
}

?>
