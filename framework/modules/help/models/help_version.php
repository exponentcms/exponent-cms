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
 * @subpackage Models
 * @package Modules
 */

class help_version extends expRecord {
//	public $table = 'help_version';
	public $validates = array(
		'uniqueness_of'=>array(
			'version'=>array('message'=>'This version number is already in use.'),
		));

    public function afterDelete() {
	    // get and delete the docs for this version
	    $help = new help();
	    $docs = $help->find('all', 'help_version_id='.$this->id);
	    foreach ($docs as $doc) {
	        $doc->delete();
	    }
    }

    public static function getCurrentHelpVersionId() {
        global $db;

        return $db->selectValue('help_version', 'id', 'is_current=1');
    }

    public static function getCurrentHelpVersion() {
        global $db;

        return $db->selectValue('help_version','version','is_current=1');
    }

    public static function getHelpVersionId($version) {
        global $db;

        return $db->selectValue('help_version', 'id', 'version="'.$db->escapeString($version).'"');
    }

    public static function getHelpVersion($version_id) {
        global $db;

        return $db->selectValue('help_version', 'version', 'id="'.intval($version_id).'"');
    }

    public static function getHelpVersionsDropdown() {
        global $db;

        return $db->selectDropdown('help_version','version',1,'version DESC');
    }

    public static function clearHelpVersion() {
        global $db;

   	    // unset the old current version.
   	    $db->toggle('help_version',"is_current",'is_current=1');
    }

}

?>