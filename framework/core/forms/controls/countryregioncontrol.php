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
    var $include_blank = false;
    var $type = 'select';

	static function name() { return "Country / Region Selector"; }

	function __construct($country_default = "", $region_default = "", $allow_entire_country = false, $disabled = false) {
        if (!is_numeric($country_default)) {
            $country_default = geoRegion::getCountryId($country_default);
        }
		$this->country_default = $country_default;
        if (!is_numeric($region_default)) {
            $region_default = geoRegion::getRegionId($region_default);
        }
		$this->region_default = $region_default;
		$this->allow_entire_country = $allow_entire_country;
		$this->disabled = $disabled;
	}

	function controlToHTML($name,$label=null) {
		$html = "";

		$countries = expGeo::listCountriesOnly();
		$c_dd = new dropdowncontrol($this->country_default, $countries);
        $c_dd->required = $this->required;
        $c_dd->disabled = $this->disabled;
        if (substr($name, -1) == ']') {
            $r_name = substr($name, 0, -1) . "_region_id]";
        } else {
            $r_name = $name . "_region_id";
        }
		$c_dd->jsHooks["onchange"] = "geo_rebuildRegions(this,'".$r_name."'," . (($this->allow_entire_country)?'true':'false') . ");";

		if (!defined('GEO_JS_INCLUDED')) {
			define('GEO_JS_INCLUDED',1);
			$html .= "<script type='text/javascript'>\n";
            $html .= " function geo_rebuildRegions(c_select,r_id,allow_all) {\n";
			$html .= "	var r_select = document.getElementById(r_id);\n";
			$html .= "	if (r_select.childNodes != null) while (r_select.childNodes.length) r_select.removeChild(r_select.firstChild);\n";

			$html .= "	var country = c_select.options[c_select.selectedIndex].value;\n";
			$html .= "  if (allow_all) {\n";
			$html .= "		var o = document.createElement('option');\n";
			$html .= "		o.setAttribute('value',0);\n";
			$html .= "		o.appendChild(document.createTextNode('[ ".gt('Entire Country')." ]'));\n";
			$html .= "		r_select.appendChild(o);";
			$html .= "	}\n";
			$html .= "  var count = 0;\n";
			$html .= "	for (i = 0; i < geo_regions.length; i++) {\n";
			$html .= "		if (geo_regions[i].var_parent_id == country) {\n";
			$html .= "			count++;\n";
			$html .= "			var o = document.createElement('option');\n";
			$html .= "			o.setAttribute('value',	geo_regions[i].var_id);\n";
			$html .= "			o.appendChild(document.createTextNode(geo_regions[i].var_name));\n";
			$html .= "			r_select.appendChild(o);\n";
			$html .= "		}\n";
			$html .= "	}\n";
			$html .= "	if (!allow_all && count == 0) {\n";
			$html .= " 		var o = document.createElement('option');\n";
			$html .= "		o.setAttribute('value',0);\n";
			$html .= "		o.appendChild(document.createTextNode('[ ".gt('None Specified')." ]'));\n";
			$html .= "		r_select.appendChild(o);\n";
			$html .= "	}\n";
			$html .= "}\n";

			$region = new stdClass();
			$region->parent_id = 0;
			$region->id = 0;
			$region->name = "";
			$html .= expJavascript::jClass($region,"geoRegion");
			$html .= "var geo_regions = new Array();\n";
			foreach ($countries as $cid=>$cname) {
				$region = new stdClass();
				$region->parent_id = $cid;
				foreach (expGeo::listRegions($cid, $this->include_blank) as $rid=>$rname) {
					$region->id = $rid;
					$region->name = $rname;
					$html .= "geo_regions.push(";
					$html .= expJavascript::jObject($region,"geoRegion");
					$html .= ");\n";
				}
			}
			$html .= "</script>\n";
		}

		$regions = expGeo::listRegions($this->country_default, $this->include_blank);
		if ($this->allow_entire_country) {
            $entire_str = gt('Entire Country');
            $regions = array(0 => "[ ".$entire_str." ]") + $regions;
		}
		elseif ($regions == null) {
            $none_str = gt('None Specified');
            $regions = array(0 => "[ ".$none_str." ]") + $regions;
		}
		$r_dd = new dropdowncontrol($this->region_default, $regions);
        $r_dd->required = $this->required;
        $r_dd->disabled = $this->disabled;

        if (substr($name, -1) == ']') {
            $c_name = substr($name, 0, -1) . "_country_id]";
        } else {
            $c_name = $name . "_country_id";
        }
		$html .= $c_dd->controlToHTML($c_name);
		$html .="<br>";
		$html .= $r_dd->controlToHTML($r_name);

		return $html;
	}

	static function parseData($name, $values, $for_db = false) {
		return;
	}

}

?>
