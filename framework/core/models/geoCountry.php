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
 * @package Core
 */
class geoCountry extends expRecord {
	public $table = 'geo_country';

    public function __construct($params=null, $get_assoc=false, $get_attached=false) {
	    global $db;
	    if (is_array($params) || is_numeric($params)) {
    	    parent::__construct($params, $get_assoc, $get_attached);
    	} else {
	        if (is_numeric($params)) echo "true dude<br>";
	        $id = $db->selectValue($this->table, 'id', "name='".$params."' OR iso_code_2letter='".$params."' OR iso_code_3letter='".$params."'" );
	        parent::__construct($id, $get_assoc, $get_attached);
	    }
	}
}

?>