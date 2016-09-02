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
 * @subpackage Controllers
 * @package    Modules
 */

class taxController extends expController {
    public $basemodel_name = 'taxclass';

    protected $add_permissions = array(
        'manage_zones' => 'Manages Zones',
        'edit_zone'    => 'Add/Edit Zone',
        'update_zone'  => 'Update Zone',
        'delete_zone'  => 'Delete Zone'
    );

    static function displayname() {
        return gt("e-Commerce Tax Class Manager");
    }

    static function description() {
        return gt("Manage tax classes for your e-Commerce store");
    }

//    static function canImportData() {
//        return true;
//    }
//
//    static function canExportData() {
//        return true;
//    }

    // tax rates

    function manage() {
        expHistory::set('manageable', $this->params);
        $taxes = taxController::getTaxRates();
        assign_to_template(array(
            'taxes' => $taxes
        ));
    }

    static function getTaxRates() {
        global $db;

        $sql = "
            SELECT
                " . $db->prefix . "tax_rate.id,
                " . $db->prefix . "tax_zone.`name` AS zonename,
                " . $db->prefix . "tax_rate.rate as rate,
                " . $db->prefix . "tax_rate.shipping_taxed as shipping_taxed,
                " . $db->prefix . "tax_rate.origin_tax as origin_tax,
                " . $db->prefix . "tax_rate.inactive as inactive,
                " . $db->prefix . "tax_class.`name` AS classname,
                " . $db->prefix . "geo_country.`name` as country,
                " . $db->prefix . "geo_region.`name` as state
            FROM " . $db->prefix . "tax_class
                INNER JOIN " . $db->prefix . "tax_rate ON " . $db->prefix . "tax_class.id = " . $db->prefix . "tax_rate.class_id
                INNER JOIN " . $db->prefix . "tax_zone ON " . $db->prefix . "tax_rate.zone_id = " . $db->prefix . "tax_zone.id
                INNER JOIN " . $db->prefix . "tax_geo ON " . $db->prefix . "tax_geo.zone_id = " . $db->prefix . "tax_zone.id
                LEFT JOIN " . $db->prefix . "geo_country ON " . $db->prefix . "tax_geo.country_id = " . $db->prefix . "geo_country.id
                LEFT JOIN " . $db->prefix . "geo_region ON " . $db->prefix . "tax_geo.region_id = " . $db->prefix . "geo_region.id
            ";

        return $db->selectObjectsBySql($sql);
    }

    function edit() {
        global $db;

        $record = '';
        if (!empty($this->params['id'])) {
            //Get the data from the 3 tables
            $tax_rate = $db->selectObject('tax_rate', 'id =' . $this->params['id']);
            $tax_class = $db->selectObject('tax_class', 'id =' . $tax_rate->class_id);
//            $tax_geo = $db->selectObject('tax_geo', 'zone_id =' . $tax_rate->zone_id);
            //Store it in a single object all the data needed
            $record = new stdClass();
            $record->id = $tax_rate->id;
            $record->class_id = $tax_rate->class_id;
            $record->classname = $tax_class->name;
            $record->rate = $tax_rate->rate;
            $record->shipping_taxed = $tax_rate->shipping_taxed;
            $record->origin_tax = $tax_rate->origin_tax;
            $record->inactive = $tax_rate->inactive;
            $record->zone = $tax_rate->zone_id;
//            $record->state = $tax_geo->region_id;
//            $record->country = $tax_geo->country_id;
        }

        //Get the tax_zone
        $records = $db->selectObjects('tax_zone');
        $zones = array();
        foreach ($records as $item) {
            $zones[$item->id] = $item->name;
        }

        $records = $db->selectObjects('tax_class');
        $classes = array();
        foreach ($records as $item) {
            $classes[$item->id] = $item->name;
        }

        assign_to_template(array(
            'classes' => $classes,
            'zones'  => $zones,
            'record' => $record
        ));
    }

    function update() {
        global $db;

//        if (isset($this->params['address_country_id'])) {
//            $this->params['country'] = $this->params['address_country_id'];
//            unset($this->params['address_country_id']);
//        }
//        if (isset($this->params['address_region_id'])) {
//            $this->params['state'] = $this->params['address_region_id'];
//            unset($this->params['address_region_id']);
//        }

        if (empty($this->params['id'])) {
            // Add data in tax class
//            $tax_class = new stdClass();
//            $tax_class->name = $this->params['name'];
//            $class_id = $db->insertObject($tax_class, 'tax_class');

            // Add data in the tax rate
            $tax_rate = new stdClass();
            $tax_rate->zone_id = $this->params['zone'];
            $tax_rate->class_id = $this->params['class'];
            $tax_rate->rate = $this->params['rate'];
            $tax_rate->shipping_taxed = $this->params['shipping_taxed'];
            $tax_rate->origin_tax = $this->params['origin_tax'];
            $tax_rate->inactive = $this->params['inactive'];
            $db->insertObject($tax_rate, 'tax_rate');

            // Add data in the tax geo
//            $tax_geo = new stdClass();
//            $tax_geo->zone_id = $this->params['zone'];
//            $tax_geo->country_id = $this->params['country'];
//            $tax_geo->region_id = $this->params['state'];
//            $db->insertObject($tax_geo, 'tax_geo');
        } else {
            // Update the Tax class table
//            $tax_class = $db->selectObject('tax_class', 'id =' . $this->params['id']);
//            $tax_class->name = $this->params['name'];
//            $db->updateObject($tax_class, 'tax_class');

            // Update the Tax rate table
            $tax_rate = $db->selectObject('tax_rate', 'id =' . $this->params['id']);
//            $zone_id = $tax_rate->zone_id;
            $tax_rate->zone_id = $this->params['zone'];
            $tax_rate->class_id = $this->params['class'];
            $tax_rate->rate = $this->params['rate'];
            $tax_rate->shipping_taxed = $this->params['shipping_taxed'] == 1;
            $tax_rate->origin_tax = $this->params['origin_tax'] == 1;
            $tax_rate->inactive = $this->params['inactive'] == 1;
            $db->updateObject($tax_rate, 'tax_rate');

            // Update the Tax geo table
//            $tax_geo = $db->selectObject('tax_geo', 'zone_id  =' . $zone_id);
//            $tax_geo->zone_id = $this->params['zone'];
//            $tax_geo->country_id = $this->params['country'];
//            $tax_geo->region_id = $this->params['state'];
//            $db->updateObject($tax_geo, 'tax_geo');
        }

        expHistory::returnTo('manageable');
    }

    /**
     * Delete tax rate
     */
    function delete() {
        global $db;

        if (empty($this->params['id'])) return false;
//        $zone = $db->selectObject('tax_zone', 'id =' . $this->params['id']);

        //Get the data from the text rate to get the zone id
//        $rate = $db->selectObject('tax_rate', 'class_id=' . $this->params['id']);

        //Delete record in tax rate
        $db->delete('tax_rate', 'class_id =' . $this->params['id']);

        //Delete record in tax geo
//        $db->delete('tax_geo', 'zone_id =' . $rate->zone_id);

        //Finally delete the record in tax class
//        $db->delete('tax_class', 'id =' . $this->params['id']);

        expHistory::returnTo('manageable');
    }

    // tax classes

    function manage_classes() {
        global $db;

        $back = expHistory::getLast('manageable');
        expHistory::set('manageable', $this->params);
        $classes = $db->selectObjects('tax_class');

        assign_to_template(array(
            'classes' => $classes,
            'back' => $back
        ));
    }

    /**
     * Edit tax class
     */
    function edit_class() {
        global $db;

        if (isset($this->params['id'])) {
            $class = $db->selectObject('tax_class', 'id =' . $this->params['id']);

            assign_to_template(array(
                'class' => $class
            ));
        }
    }

    /**
     * Update tax class
     */
    function update_class() {
        global $db;

        if (empty($this->params['id'])) {
             // Add data in tax class
            $obj = new stdClass();
            $obj->name = $this->params['name'];
            $db->insertObject($obj, 'tax_class');
        } else {
            // Update the Tax class table
            $class = $db->selectObject('tax_class', 'id =' . $this->params['id']);
            $class->name = $this->params['name'];
            $db->updateObject($class, 'tax_class');
        }

        expHistory::returnTo('manageable');
    }

    /**
     * Delete tax class along with assoc. tax rates
     */
    function delete_class() {
        global $db;

        if (empty($this->params['id'])) return false;
        $db->delete('tax_rate', 'class_id =' . $this->params['id']);
        $db->delete('tax_class', 'id =' . $this->params['id']);

        expHistory::returnTo('manageable');
    }

    // tax zones

    function manage_zones() {
        global $db;

        $back = expHistory::getLast('manageable');
        expHistory::set('manageable', $this->params);
//        $zones = $db->selectObjects('tax_zone', null, 'name');
        $zones = taxController::getTaxZones();

        assign_to_template(array(
            'zones' => $zones,
            'back' => $back
        ));
    }

    static function getTaxZones() {
        global $db;

        $sql = "
            SELECT
                " . $db->prefix . "tax_zone.id,
                " . $db->prefix . "tax_zone.`name` AS name,
                " . $db->prefix . "geo_country.`name` as country,
                " . $db->prefix . "geo_region.`name` as state
            FROM " . $db->prefix . "tax_zone
                INNER JOIN " . $db->prefix . "tax_geo ON " . $db->prefix . "tax_geo.zone_id = " . $db->prefix . "tax_zone.id
                LEFT JOIN " . $db->prefix . "geo_country ON " . $db->prefix . "tax_geo.country_id = " . $db->prefix . "geo_country.id
                LEFT JOIN " . $db->prefix . "geo_region ON " . $db->prefix . "tax_geo.region_id = " . $db->prefix . "geo_region.id
            ";

        return $db->selectObjectsBySql($sql);
    }

    /**
     * Edit tax zone and tax geo
     */
    function edit_zone() {
        global $db;

        if (isset($this->params['id'])) {
            $zone = $db->selectObject('tax_zone', 'id =' . $this->params['id']);

            $tax_geo = $db->selectObject('tax_geo', 'zone_id =' . $zone->id);
            //Store it in a single object all the data needed
            $zone->state = $tax_geo->region_id;
            $zone->country = $tax_geo->country_id;

            assign_to_template(array(
                'zone' => $zone
            ));
        }
    }

    /**
     * Update tax zone and assoc. tax geo
     */
    function update_zone() {
        global $db;

        if (isset($this->params['address_country_id'])) {
            $this->params['country'] = $this->params['address_country_id'];
            unset($this->params['address_country_id']);
        }
        if (isset($this->params['address_region_id'])) {
            $this->params['state'] = $this->params['address_region_id'];
            unset($this->params['address_region_id']);
        }

        if (empty($this->params['id'])) {
             // Add data in tax zone
            $obj = new stdClass();
            $obj->name = $this->params['name'];
            $zone_id = $db->insertObject($obj, 'tax_zone');

            // Add data in the tax geo
            $tax_geo = new stdClass();
            $tax_geo->zone_id = $zone_id;
            $tax_geo->country_id = $this->params['country'];
            $tax_geo->region_id = $this->params['state'];
            $db->insertObject($tax_geo, 'tax_geo');
        } else {
            // Update the Tax zone table
            $zone = $db->selectObject('tax_zone', 'id =' . $this->params['id']);
            $zone->name = $this->params['name'];
            $db->updateObject($zone, 'tax_zone');

            // Update the Tax geo table
            $tax_geo = $db->selectObject('tax_geo', 'zone_id  =' . $zone->id);
//            $tax_geo->zone_id = $this->params['id'];
            $tax_geo->country_id = $this->params['country'];
            $tax_geo->region_id = $this->params['state'];
            $db->updateObject($tax_geo, 'tax_geo');
        }

        expHistory::returnTo('manageable');
    }

    /**
     * Delete tax zone alone with assoc. tax classes & tax rates
     */
    function delete_zone() {
        global $db;

        if (empty($this->params['id'])) return false;
        $db->delete('tax_geo', 'zone_id =' . $this->params['id']);
        $rates = $db->selectObjects('tax_rate', 'zone_id =' . $this->params['id']);
        foreach ($rates as $rate) {
            $db->delete('tax_class', 'id =' . $rate->id);
        }
        $db->delete('tax_rate', 'zone_id =' . $this->params['id']);
        $db->delete('tax_zone', 'id =' . $this->params['id']);

        expHistory::returnTo('manageable');
    }

}

?>