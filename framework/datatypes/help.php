<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class help extends expRecord {
	public $table = 'help';
	public $has_one = array('help_version');

	public function __construct($params=array()) {
        parent::__construct($params);
        $this->loc = expUnserialize($this->location_data);
    }

	public function save() {
        global $db;

		// manipulate section & location_data to correct values
        $hlpsection = $db->selectObject('sectionref','module = "helpController" AND source = "'.$_POST['section'].'"');
		$this->section = $hlpsection->section;
		$loc = null;
		$loc->mod = help;
		$loc->src = $_POST['section'];
		$loc->int = '';
		$this->location_data = serialize($loc);

        parent::save();
   }

    public function makeSefUrl() {
        global $router, $db;
        
        $sef_params = '';
        if (isset($this->title)) {
            $sef_params .= $this->title;
        } else {
	        $sef_params .= 'Untitled';
        }

        if (isset($this->help_version_id)) {
            $version = $db->selectValue('help_version', 'version', 'id='.$this->help_version_id);
            $sef_params .= " Version ".$version;
        }

        $this->sef_url = $router->encode($sef_params);

		$dupe = $db->selectValue($this->tablename, 'sef_url', 'sef_url="'.$this->sef_url.'"');
		if (!empty($dupe)) {
			list($u, $s) = explode(' ',microtime());
			$this->sef_url .= '-'.$s.'-'.$u;
		}
    }
    
    public static function makeHelpLink($module) {
        // make sure the module name is in the right format.
        $module = getControllerName($module);
        
        // figure out which version we're on
        $full_version = EXPONENT_VERSION_MAJOR.'.'.EXPONENT_VERSION_MINOR.'.'.EXPONENT_VERSION_REVISION.EXPONENT_VERSION_TYPE;

        $link  = HELP_URL;
        $link .= 'docs';
        $link .= '/'.$full_version;
        $link .= '/'.$module;
        
        return $link;
    }
}

?>
