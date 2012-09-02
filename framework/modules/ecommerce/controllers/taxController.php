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
 * @subpackage Controllers
 * @package Modules
 */

class taxController extends expController {
    public $basemodel_name = 'taxclass';
        
    protected $add_permissions = array(
			'manage_zones'  =>'Manages Zones',
			'edit_zone'     =>'Add/Edit Zone',
			'update_zone'   =>'Update Zone',
			'delete_zone'   =>'Delete Zone'
		);
		
    static function displayname() { return gt("e-Commerce Tax Class Manager"); }
    static function description() { return gt("Manage tax classes for your Ecommerce store"); }
    static function canImportData() { return true; }
    static function canExportData() { return true; }

    function manage() {               
        expHistory::set('manageable', $this->params);
        $taxes = taxController::getTaxClasses();
        assign_to_template(array(
            'taxes'=>$taxes
        ));
    }
    
    static function getTaxClasses()
    {
        global $db;
        $sql = "

            SELECT 
                ".DB_TABLE_PREFIX."_tax_class.id, 
                ".DB_TABLE_PREFIX."_tax_zone.`name` AS zonename, 
                ".DB_TABLE_PREFIX."_tax_rate.rate as rate, 
                ".DB_TABLE_PREFIX."_tax_class.`name` AS classname, 
                ".DB_TABLE_PREFIX."_geo_country.`name` as country, 
                ".DB_TABLE_PREFIX."_geo_region.`name` as state
            FROM ".DB_TABLE_PREFIX."_tax_class INNER JOIN ".DB_TABLE_PREFIX."_tax_rate ON ".DB_TABLE_PREFIX."_tax_class.id = ".DB_TABLE_PREFIX."_tax_rate.class_id
                 INNER JOIN ".DB_TABLE_PREFIX."_tax_zone ON ".DB_TABLE_PREFIX."_tax_rate.zone_id = ".DB_TABLE_PREFIX."_tax_zone.id
                 INNER JOIN ".DB_TABLE_PREFIX."_tax_geo ON ".DB_TABLE_PREFIX."_tax_geo.zone_id = ".DB_TABLE_PREFIX."_tax_zone.id
                 INNER JOIN ".DB_TABLE_PREFIX."_geo_country ON ".DB_TABLE_PREFIX."_tax_geo.country_id = ".DB_TABLE_PREFIX."_geo_country.id
                 INNER JOIN ".DB_TABLE_PREFIX."_geo_region ON ".DB_TABLE_PREFIX."_tax_geo.region_id = ".DB_TABLE_PREFIX."_geo_region.id

            ";

        return $db->selectObjectsBySql($sql);
    }

	function edit() {
		global $db;
		$record = '';
		if(!empty($this->params['id'])) {
			//Get the data from the 3 tables
			$tax_class = $db->selectObject('tax_class', 'id =' .$this->params['id']);
			$tax_rate  = $db->selectObject('tax_rate', 'class_id =' .$this->params['id']);
			$tax_geo   = $db->selectObject('tax_geo', 'zone_id =' . $tax_rate->zone_id);
			//Store it in a single object all the data needed
			$record->id        = $tax_class->id;
			$record->classname = $tax_class->name;
			$record->rate      = $tax_rate->rate;
			$record->zone      = $tax_rate->zone_id;
			$record->state     = $tax_geo->region_id; 
			$record->country   = $tax_geo->country_id;
		}
		
		//Get the tax_zone
		$records = $db->selectObjects('tax_zone');
        $zones = array();
		foreach($records as $item) {
			$zones[$item->id] = $item->name;
		}

		assign_to_template(array(
            'zones'=>$zones,
            'record'=>$record
        ));
	}
	
	function update() {
		global $db;
			
		if(empty($this->params['id'])) {
			//Add data in tax class
			$tax_class->name = $this->params['name'];
			$class_id        = $db->insertObject($tax_class,'tax_class');
			
			//Add data in the tax geo
			$tax_geo->zone_id    = $this->params['zone'];
			$tax_geo->country_id = $this->params['country'];
			$tax_geo->region_id  = $this->params['state'];
			$db->insertObject($tax_geo,'tax_geo');
			
			//Add data in the tax rate
			$tax_rate->zone_id  = $this->params['zone'];
			$tax_rate->class_id = $class_id;
			$tax_rate->rate     = $this->params['rate'];
			$db->insertObject($tax_rate,'tax_rate');
		} else {
		
			//Update the Tax class table
			$tax_class        = $db->selectObject('tax_class', 'id =' .$this->params['id']);
			$tax_class->name  = $this->params['name'];
			$db->updateObject($tax_class, 'tax_class');
		
			//Update the Tax rate table
			$tax_rate           = $db->selectObject('tax_rate', 'class_id =' .$this->params['id']);
			$zone_id            = $tax_rate->zone_id;
			$tax_rate->zone_id  = $this->params['zone'];
			$tax_rate->rate     = $this->params['rate'];
			$db->updateObject($tax_rate,'tax_rate');
			
			//Update the Tax geo table
			$tax_geo             = $db->selectObject('tax_geo', 'zone_id  =' .$zone_id);
			$tax_geo->zone_id    = $this->params['zone'];
			$tax_geo->country_id = $this->params['country'];
			$tax_geo->region_id  = $this->params['state'];
			$db->updateObject($tax_geo,'tax_geo');
		}
		
        expHistory::back();
	}
	
	function delete() {
		global $db;
		
        if (empty($this->params['id'])) return false;
        $zone = $db->selectObject('tax_zone', 'id =' .$this->params['id']);
		
		//Get the data from the text rate to get the zone id
		$rate = $db->selectObject('tax_rate', 'class_id=' . $this->params['id']);
		
		//Delete record in tax rate
        $db->delete('tax_rate', 'class_id =' .$this->params['id']);
		
		//Delete record in tax geo
		$db->delete('tax_geo', 'zone_id =' . $rate->zone_id);
		
		//Finally delete the record in tax class
		$db->delete('tax_class', 'id =' .$this->params['id']);
		
		
        expHistory::back();
	
	}
	
    function manage_zones() {
        global $db;
        
        expHistory::set('manageable', $this->params);
        $zones = $db->selectObjects('tax_zone', null, 'name');

        assign_to_template(array(
            'zones'=>$zones
        ));
    }
   
    function edit_zone() {
        global $db;
		
		if(isset($this->params['id'])) {
			$zone = $db->selectObject('tax_zone', 'id =' .$this->params['id']);
			assign_to_template(array(
                'zone'=>$zone
            ));
		}
    }

    function update_zone() {
        global $db;
			
		if(empty($this->params['id'])) {
			$obj->name = $this->params['name'];
			$db->insertObject($obj,'tax_zone');
		} else {
			$zone        = $db->selectObject('tax_zone', 'id =' .$this->params['id']);
			$zone->name  = $this->params['name'];
			$db->updateObject($zone, 'tax_zone');
		}
		
        expHistory::back();
    }
	
	function delete_zone() {
		global $db;
		
        if (empty($this->params['id'])) return false;
        $db->delete('tax_zone', 'id =' .$this->params['id']);
		
        expHistory::back();
    }
}

?>