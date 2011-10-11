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
    
    // public function beforeSave($params=array()) {  
    //     eDebug($this,1);
    //     //$this->save(true);  
    // }
	
	public function save() {
        global $db;

		if (isset($_POST['section'])) {
			// manipulate section & location_data to correct values
			$hlpsection = $db->selectObject('sectionref','module = "helpController" AND source = "'.$_POST['section'].'"');
			$this->section = $hlpsection->section;
			$loc = null;
			$loc->mod = 'help';
			$loc->src = $_POST['section'];
			$loc->int = '';
			$this->location_data = serialize($loc);
		}

        parent::save(true);
   }

	/**
	 * Make a unique help item sef-url within the version_version
	 */
	public function makeSefUrl() {
		global $router, $db;

        if (isset($this->title)) {
			$this->sef_url = $router->encode($this->title);
		} else {
			$this->sef_url = $router->encode('Untitled');
		}
        $dupe = $db->selectValue($this->tablename, 'sef_url', 'sef_url="'.$this->sef_url.' AND help_version_id = '.$this->help_version_id.'"');
		if (!empty($dupe)) {
			list($u, $s) = explode(' ',microtime());
			$this->sef_url .= '-'.$s.'-'.$u;
		}
	}

	/**
	 * validate help item sef_url within the help_version
	 * @return bool
	 */
	public function validate() {
        global $db;
        // check for an sef url field.  If it exists make sure it's valid and not a duplicate
        //this needs to check for SEF URLS being turned on also: TODO

        if (property_exists($this, 'sef_url') && !(in_array('sef_url',$this->do_not_validate))) {
            if (empty($this->sef_url)) $this->makeSefUrl();
            $this->validates['is_valid_sef_name']['sef_url'] = array();
            $this->validates['uniqueness_of']['sef_url'] = array();
        }

        // safeguard again loc data not being pass via forms...sometimes this happens when you're in a router
        // mapped view and src hasn't been passed in via link to the form
        if (isset($this->id) && empty($this->location_data)) {
            $loc = $db->selectValue($this->tablename, 'location_data', 'id='.$this->id);
            if (!empty($loc)) $this->location_data = $loc;
        }

        // run the validation as defined in the datatypes
        if (!isset($this->validates)) return true;
        $messages = array();
        $post = empty($_POST) ? array() : $_POST;
        foreach ($this->validates as $validation=>$field) {
            foreach($field as $key=>$value) {
                $fieldname = is_numeric($key) ? $value : $key;
                $opts = is_numeric($key) ? array() : $value;
                $sql = "`".$fieldname."`='".$this->$fieldname.' AND help_version_id = '.$this->help_version_id.'"';
                if (!empty($this->id)) $sql .= ' AND id != '.$this->id;
                $ret = $db->countObjects($this->tablename, $sql);
                if ($ret > 0) {
                    $ret = array_key_exists('message', $opts) ? $opts['message'] : ucwords($fieldname).' "'.$this->$fieldname.'" is already in use.';
                } else {
                    $ret = true;
                }
                if(!is_bool($ret)) {
                    $messages[] = $ret;
                    expValidator::setErrorField($fieldname);
                    unset($post[$fieldname]);
                }
            }
        }

        if (count($messages) >= 1) expValidator::failAndReturnToForm($messages, $post);
    }

    public static function makeHelpLink($module) {
        // make sure the module name is in the right format.
        $module = expModules::getControllerName($module);
        
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
