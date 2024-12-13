<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * This is the class expSubsystem
 * Exponent Subsystem
 *
 * The expSubsystem class is used to provide basic methods to subsystem classes.
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
#[AllowDynamicProperties]
class expSubsystem {

    /**
     * Generic magic method
     *
     * @param $property
     * @return null
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return null;
    }

    /**
     *  Generic magic method
     *  We MUST create/set non-existing properties for Exponent code to work
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value) {
//        if (property_exists($this, $property)) {
            $this->$property = $value;
//        }
    }

    /**
     * Generic magic method
     *
     * @param $property
     * @return bool
     */
    public function  __isset($property) {
        return isset($this->$property);
    }

    /**
     * Generic magic method
     *
     * @param $property
     */
    public function __unset($property) {
        unset($this->$property);
    }

}

?>