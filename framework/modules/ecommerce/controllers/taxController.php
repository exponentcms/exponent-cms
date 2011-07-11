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
            	exponent_tax_class.id, 
            	exponent_tax_zone.`name` AS zonename, 
            	exponent_tax_rate.rate as rate, 
            	exponent_tax_class.`name` AS classname, 
            	exponent_geo_country.`name` as country, 
            	exponent_geo_region.`name` as state
            FROM exponent_tax_class INNER JOIN exponent_tax_rate ON exponent_tax_class.id = exponent_tax_rate.class_id
            	 INNER JOIN exponent_tax_zone ON exponent_tax_rate.zone_id = exponent_tax_zone.id
            	 INNER JOIN exponent_tax_geo ON exponent_tax_geo.zone_id = exponent_tax_zone.id
            	 INNER JOIN exponent_geo_country ON exponent_tax_geo.country_id = exponent_geo_country.id
            	 INNER JOIN exponent_geo_region ON exponent_tax_geo.region_id = exponent_geo_region.id

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
            FROM exponent_tax_zone ORDER BY name ASC;

            ";

        $zones = $db->selectObjectsBySql($sql);

        assign_to_template(array('zones'=>$zones));
    }
   
    function edit_zone() {
        global $db;
                
        $sql = "

            SELECT 
            *
            FROM exponent_tax_zone ORDER BY name ASC;

            ";

        $zones = $db->selectObjectsBySql($sql);

        assign_to_template(array('zones'=>$zones));
    }

    function update_zone() {
        global $db;
        $obj->name = $this->params['name'];
        $db->insertObject($obj,'tax_zone');
        expHistory::back();
    }
   
}

?>
