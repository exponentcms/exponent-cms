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
 * This is the class expCoonfig
 *
 * @subpackage Models
 * @package Core
 */

class expConfig extends expRecord {
	protected $table = 'expConfigs';

	function __construct($params=null) {
		global $db;

        if (!is_array($params)) {
            $this->location_data = serialize($params);
            parent::__construct($db->selectValue($this->table, 'id', "location_data='".$this->location_data."'"));
        } else {
            parent::__construct($params);
        }
				
		// treat the loc data like an id - if the location data come thru as an object we need to look up the record
            //         if (!empty($params->src)) {
            //             echo "1";
            //             // if we hav a src, ie this controller has sources
            // parent::__construct($db->selectValue($this->table, 'id', "location_data='".$this->location_data."'"));
            //         } else {
            //             echo "2";
            //             // if we don't have a sourced controller, might still have a config for it.
            // parent::__construct($db->selectValue($this->table, 'id'));
            //}
		$this->config = expUnserialize($this->config);
	}

    // extend the parent update() function to serialize the config
    // data before we build the object to be saved.
    public function update($params=array()) {
        foreach($params['config'] as $key => $value) {
            if (substr($key,-5) == '_list') $params['config'][$key] = listbuildercontrol::parseData($params['config'],$key,true);
        }
        if(is_array($params['config'])) {
            $params['config'] = serialize($params['config']);
        }
        parent::update($params);
    }
    
	public function beforeSave() {
	    // one last check to make sure the data is in the proper format.
		$this->location_data = (is_object($this->location_data)) ? serialize($this->location_data) : $this->location_data;
		$this->config = (is_array($this->config)) ? serialize($this->config) : $this->config;
	}

    public static function getConfig($loc) {
        global  $db;

        if (is_object($loc)) {
            $the_loc = serialize($loc);
        } else {
            $the_loc = $loc;
        }
        return expUnserialize($db->selectValue('expConfigs','config',"location_data='".$the_loc."'"));

    }

}

?>