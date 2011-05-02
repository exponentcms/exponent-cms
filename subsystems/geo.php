<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

/* exdoc
 * The definition of this constant lets other parts of the subsystem know
 * that the Geo Subsystem has been included for use.
 * @node Subsystems:Geo
 */
define("SYS_GEO",1);

/* exdoc
 * List all Countries in the Geo Database.  Returns an array of country objects.
 *
 * @node Subsystems:Geo
 */
function exponent_geo_listCountriesOnly() {
	global $db;
	$countries = array();
	foreach ($db->selectObjects("geo_country") as $c) {
		$countries[$c->id] = $c->name;
	}
	uasort($countries,"strnatcasecmp");
	return $countries;
}

/* exdoc
 * List Countries and Regions in the Geo Database.  Returns a two-tiered array of countries and regions.
 * @node Subsystems:Geo
 */
function exponent_geo_listCountriesAndRegions() {
	global $db;
	$countries = array();
	foreach ($db->selectObjects("geo_country") as $c) {
		$countries[$c->id] = null;
		$countries[$c->id]->name = $c->name;
		$countries[$c->id]->regions = array();
		
		foreach ($db->selectObjects("geo_region","country_id=".$c->id) as $r) {
			$countries[$c->id]->regions[$r->id] = $r->name;
		}
		uasort($countries[$c->id]->regions,"strnatcasecmp");
	}
	if (!defined("SYS_SORTING")) require_once(BASE."subsystems/sorting.php");
	uasort($countries,"exponent_sorting_byNameAscending");
	return $countries;
}

/* exdoc
 * List Regions for a specific Country. Returns an array of regions.
 *
 * @param integer $country_id The id of the country to get regions for
 * @node Subsystems:Geo
 */
function exponent_geo_listRegions($country_id) {
	global $db;
	$regions = array();
	foreach ($db->selectObjects("geo_region","country_id=".$country_id) as $r) {
		$regions[$r->id] = $r->name;
	}
	uasort($regions,"strnatcasecmp");
	return $regions;
}

/* exdoc
 * List all Regions in the Geo Database.  Returns an array of regions
 * @node Subsystems:Geo
 */
function exponent_geo_listAllRegions() {
	global $db;
	$regions = array();
	foreach ($db->selectObjects("geo_region") as $r) {
		$regions[$r->id] = $r->name;
	}
	return $regions;
}

?>