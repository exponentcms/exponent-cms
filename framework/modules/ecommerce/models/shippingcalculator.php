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
 * @subpackage Models
 * @package    Core
 */
class shippingcalculator extends expRecord {
    public $table = 'shippingcalculator';
    public $icon = '';
    public $configdata = array();

    public function addressRequired() {
        return true;
    }

    public function __construct($params = null, $get_assoc = true, $get_attached = true) {
        parent::__construct($params, $get_assoc, $get_attached);

        // grab the config data for this calculator
        $this->configdata = empty($this->config) ? array() : unserialize($this->config);

        if (file_exists(BASE . 'framework/modules/ecommerce/shippingcalculators/icons/' . $this->classname . '.gif')) {
            $this->icon = PATH_RELATIVE . 'framework/modules/ecommerce/shippingcalculators/icons/' . $this->classname . '.gif';
        } else {
            $this->icon = PATH_RELATIVE . 'framework/modules/ecommerce/shippingcalculators/icons/default.png';
        }

    }

    public function meetsCriteria() {
        return true;
    }

    public function getHandling() {
        return 0;
    }

    /**
     * Return default shipping calculator
     *
     */
    public static function getDefault() {
        global $db;

        $calc = $db->selectObject('shippingcalculator','is_default=1');
        if (empty($calc)) $calc = $db->selectObject('shippingcalculator','enabled=1');
        if (!empty($calc->id)) return $calc->id;
        else return false;
    }

}

?>