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

    public static function getId($state) {
        global $db;
        return $db->selectValue('geo_region', 'id', "name='".$state."' OR code='".$state."'" );
    }
    
    public static function getAbbrev($id) {
        global $db;
        return $db->selectValue('geo_region', 'code', 'id='.$id);
    }
       
    public static function getName($id) {
        global $db;
        return $db->selectValue('geo_region', 'code', 'id='.$id);
    }
    
    public static function getCountryCode($id) {
        global $db;
        $countryid = $db->selectValue('geo_region', 'country_id', 'id='.$id);
        return $db->selectValue('geo_country', 'iso_code_2letter', 'id='.$countryid);
    }
    
    public static function getCountryName($id) {
        global $db;
        $countryid = $db->selectValue('geo_region', 'country_id', 'id='.$id);
        return $db->selectValue('geo_country', 'name', 'id='.$countryid);
    }
 
}

?>