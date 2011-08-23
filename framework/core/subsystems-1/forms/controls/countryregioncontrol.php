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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Country Region Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class countryregioncontrol extends formcontrol {

	var $size = 0;
	var $maxlength = "";

	function name() { return "Country / Region Selector"; }

	function __construct($country_default = "", $region_default = "", $allow_entire_country = false, $disabled = false) {
		$this->country_default = $country_default;
		$this->region_default = $region_default;
		$this->allow_entire_country = $allow_entire_country;
		$this->disabled = $disabled;
	}

	function controlToHTML($name) {
		$html = "";

		require_once(BASE."framework/core/subsystems-1/geo.php");
		$countries = exponent_geo_listCountriesOnly();
		$c_dd = new dropdowncontrol($this->country_default,$countries);
		$c_dd->jsHooks["onchange"] = "geo_rebuildRegions(this,'".$name."_region_id'," . (($this->allow_entire_country)?'true':'false') . ");";


		if (!defined("GEO_JS_INCLUDED")) {
			define("GEO_JS_INCLUDED",1);
			$html .= "<script language='JavaScript'>function geo_rebuildRegions(c_select,r_id,allow_all) {";
			$html .= "	var r_select = document.getElementById(r_id);";
			$html .= "	while (r_select.childNodes.length) r_select.removeChild(r_select.firstChild);";

			$html .= "	var country = c_select.options[c_select.selectedIndex].value;";
			//alert(country);
			$html .= "   if (allow_all) {";
			$html .= "		var o = document.createElement('option');";
			$html .= "		o.setAttribute('value',0);";
			$html .= "		o.appendChild(document.createTextNode('[ Entire Country ]'));";
			$html .= "		r_select.appendChild(o);";
			$html .= "	}";
			$html .= "   var count = 0;";
			$html .= "	for (i = 0; i < geo_regions.length; i++) {";
			$html .= "		if (geo_regions[i].var_parent_id == country) {";
			$html .= "			count++;";
			$html .= "			var o = document.createElement('option');";
			$html .= "			o.setAttribute('value',	geo_regions[i].var_id);";
			$html .= "			o.appendChild(document.createTextNode(geo_regions[i].var_name));";
			$html .= "			r_select.appendChild(o);";
			$html .= "		}";
			$html .= "	}";
			$html .= "	if (!allow_all && count == 0) {";
			$html .= " 		var o = document.createElement('option');";
			$html .= "		o.setAttribute('value',0);";
			$html .= "		o.appendChild(document.createTextNode('[ None Specified ]'));";
			$html .= "		r_select.appendChild(o);";
			$html .= "	}";
			$html .= "}";
//			require_once(BASE."framework/core/subsystems-1/javascript.php");
			$region = null;
			$region->parent_id = 0;
			$region->id = 0;
			$region->name = "";
//			$html .= exponent_javascript_class($region,"geoRegion");
			$html .= expJavascript::jClass($region,"geoRegion");
			$html .= "var geo_regions = new Array();\n";
			foreach ($countries as $cid=>$cname) {
				$region = null;
				$region->parent_id = $cid;
				foreach (exponent_geo_listRegions($cid) as $rid=>$rname) {
					$region->id = $rid;
					$region->name = $rname;
					$html .= "geo_regions.push(";
//					$html .= exponent_javascript_object($region,"geoRegion");
					$html .= expJavascript::jObject($region,"geoRegion");
					$html .= ");\n";
				}
			}
			$html .= "</script>\n";

		}

		$regions = exponent_geo_listRegions($this->country_default);
		if ($this->allow_entire_country) {
			array_unshift($regions,"[ Entire Country ]");
		}
		elseif ($regions == null) {
			array_unshift($regions,"[ None Specified ]");
		}
		$r_dd = new dropdowncontrol($this->region_default,$regions);

		$html .= $c_dd->controlToHTML($name."_country_id");
		$html .="<br>";
		$html .= $r_dd->controlToHTML($name."_region_id");

		return $html;
	}

	static function parseData($name, $values, $for_db = false) {
		return;
	}

}

?>
