<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
class geoRegion extends expRecord {
	public $table = 'geo_region';
	
	public function __construct($params=null, $get_assoc=false, $get_attached=false) {
	    global $db;

	    if (is_array($params) || is_numeric($params)) {
	        parent::__construct($params, $get_assoc, $get_attached);
	    } else {
	        $id = $db->selectValue($this->table, 'id', "name='".$params."' OR code='".$params."'" );
	        parent::__construct($id);
	    }
	}

    /**
     * Get state id from state name or state code
     *
     * @param $state
     * @return null
     */
    public static function getRegionId($state) {
        global $db;

        return $db->selectValue('geo_region', 'id', "name='".$state."' OR code='".$state."'" );
    }

    /**
     * Get state abbreviation from state id
     *
     * @param $id
     * @return null
     */
    public static function getAbbrev($id) {
        global $db;

        return $db->selectValue('geo_region', 'code', 'id='.$id);
    }

    /**
     * Get state name from state id
     *
     * @param $id
     * @return null
     */
    public static function getName($id) {
        global $db;

        return $db->selectValue('geo_region', 'name', 'id='.$id);
    }

    /**
     * Get country 2 letter iso code from country id
     *
     * @param $id
     * @return null
     */
    public static function getCountryCode($id) {
        global $db;

//        $countryid = $db->selectValue('geo_region', 'country_id', 'id='.$id);
//        return $db->selectValue('geo_country', 'iso_code_2letter', 'id='.$countryid);
        return $db->selectValue('geo_country', 'iso_code_2letter', 'id='.$id);
    }

    /**
     * Get country name from country id
     *
     * @param $id
     * @return null
     */
    public static function getCountryName($id) {
        global $db;

//        $countryid = $db->selectValue('geo_region', 'country_id', 'id='.$id);
//        return $db->selectValue('geo_country', 'name', 'id='.$countryid);
        return $db->selectValue('geo_country', 'name', 'id='.$id);
    }

    /**
     * Get country id from country name or iso code
     *
     * @param $country
     * @return null
     */
    public static function getCountryId($country) {
        global $db;

        return $db->selectValue('geo_country', 'id', "name='".$country."' OR iso_code_2letter='".$country."'" );
    }

}

?>