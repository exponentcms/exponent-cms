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
 * This is the class expGeo
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */

/** @define "BASE" "../../.." */
class expGeo
{

    /** exdoc
     * List active Countries in the Geo Database.  Returns an array of country objects.
     *
     * @node Subsystems:expGeo
     * @return array
     */
    public static function listCountriesOnly()
    {
        global $db;

        $countries = array();
        foreach ($db->selectObjects("geo_country", "active=1") as $c) {
            $countries[$c->id] = $c->name;
        }
        uasort($countries, "strnatcasecmp");
        return $countries;
    }

    /** exdoc
     * List all Countries in the Geo Database.  Returns an array of country objects.
     *
     * @node Subsystems:expGeo
     * @return array
     */
    public static function listAllCountriesOnly()
    {
        global $db;

        $countries = array();
        foreach ($db->selectObjects("geo_country") as $c) {
            $countries[$c->id] = $c->name;
        }
        uasort($countries, "strnatcasecmp");
        return $countries;
    }

    /** exdoc
     * List active Regions for a specific Country. Returns an array of region objects.
     *
     * @param integer $country_id The id of the country to get regions for
     *
     * @node Subsystems:expGeo
     * @return array
     */
    public static function listRegions($country_id, $include_blank = null)
    {
        global $db;

        $regions = array();
        foreach ($db->selectObjects("geo_region", "country_id=" . $country_id . " AND active=1") as $r) {
            $regions[$r->id] = $r->name;
        }
        uasort($regions, "strnatcasecmp");
        if (!empty($regions) && !empty($include_blank)) {
            $regions = array(""=>$include_blank) + $regions;
        }
        return $regions;
    }

    /** exdoc
     * List all Regions in the Geo Database.  Returns an array of regions
     *
     * @node Subsystems:expGeo
     * @return array
     */
    public static function listAllRegions()
    {
        global $db;

        $regions = array();
        foreach ($db->selectObjects("geo_region") as $r) {
            $regions[$r->id] = $r->name;
        }
        return $regions;
    }

    /** exdoc
     * List active Countries and Regions in the Geo Database.  Returns a two-tiered array of countries and regions.
     *
     * @node Subsystems:expGeo
     * @return array
     */
    public static function listCountriesAndRegions()
    {
        global $db;

        $countries = array();
        foreach ($db->selectObjects("geo_country", "active=1") as $c) {
            $countries[$c->id] = new stdClass();
            $countries[$c->id]->name = $c->name;
            $countries[$c->id]->regions = array();

            foreach ($db->selectObjects("geo_region", "country_id=" . $c->id . " AND active=1") as $r) {
                $countries[$c->id]->regions[$r->id] = $r->name;
            }
            uasort($countries[$c->id]->regions, "strnatcasecmp");
        }
        $countries = expSorter::sort(
            array('array' => $countries, 'sortby' => 'name', 'order' => 'ASC', 'ignore_case' => true, 'type' => 'a')
        );
        return $countries;
    }

}

?>