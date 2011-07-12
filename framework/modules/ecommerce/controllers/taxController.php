<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Created by Adam Kessler @ 05/28/2008
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

class taxController extends expController {
    public $basemodel_name = 'taxclass';
        
    //protected $permissions = array_merge(array("test"=>'Test'), array('copyProduct'=>"Copy Product"));
    //protected $add_permissions = array();
    public $useractions = null; // keeps it from showing up in available modules to activate
     
    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "e-Commerce Tax Class Manager"; }
    function description() { return "Manage tax classes for your Ecommerce store"; }
    function author() { return "OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return true; }
    function canImportData() { return true; }
    function canExportData() { return true; }

    function manage() {
        global $db;
        
        expHistory::set('managable', $this->params);
        
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

        $taxes = $db->selectObjectsBySql($sql);
        assign_to_template(array('taxes'=>$taxes));
    }

    function manage_zones() {
        global $db;
        
        expHistory::set('managable', $this->params);
        
        $sql = "

            SELECT 
            *
            FROM ".DB_TABLE_PREFIX."_tax_zone ORDER BY name ASC;

            ";

        $zones = $db->selectObjectsBySql($sql);

        assign_to_template(array('zones'=>$zones));
    }
   
    function edit_zone() {
        global $db;
		
		if(isset($this->params['id'])) {
			$zone = $db->selectObject('tax_zone', 'id =' .$this->params['id']);
			assign_to_template(array('zone'=>$zone));
		}
    }

    function update_zone() {
        global $db;
			
		if(empty($this->params['id'])) {
			$obj->name = $this->params['name'];
			$db->insertObject($obj,'tax_zone');
		} else {
			$zone = $db->selectObject('tax_zone', 'id =' .$this->params['id']);
			$zone->name  = $this->params['name'];
			$db->updateObject($zone, 'tax_zone');
		}
		
        expHistory::back();
    }
	
	function delete_zone() {
		global $db;
		
        if (empty($this->params['id'])) return false;
        $zone = $db->selectObject('tax_zone', 'id =' .$this->params['id']);
        $db->delete('tax_zone', 'id =' .$this->params['id']);
		
        expHistory::back();
    }
}

?>