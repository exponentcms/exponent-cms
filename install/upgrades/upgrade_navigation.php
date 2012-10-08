<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class upgrade_navigation
 */
class upgrade_navigation extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.0.9';
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Upgrade the Navigation module to a Controller"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Navigation module was upgraded to a Controller in v2.0.9.".
        "This Script converts Navigation modules to the new format and then deletes most old navigationmodule files except those used for backward compatibility."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        return true;
//        if (expUtil::isReallyWritable(BASE."framework/modules-1/navigationmodule/actions/")) {
//            return true;  // the old files still exist
//        } else return false;
   	}

	/**
	 * converts all navigationmodule modules/items into simplePoll (controller) modules/items and deletes navigationmodule files
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each navigationmodule reference to a navigation Controller reference
	    $srs = $db->selectObjects('sectionref',"module = 'navigationmodule'");
	    foreach ($srs as $sr) {
		    $sr->module = 'navigationController';
		    $db->updateObject($sr,'sectionref');
	    }
	    $gps = $db->selectObjects('grouppermission',"module = 'navigationmodule'");
        foreach ($gps as $gp) {
	        $gp->module = 'navigationController';
	        $db->updateObject($gp,'grouppermission',null,'gid');
        }
        $ups = $db->selectObjects('userpermission',"module = 'navigationmodule'");
        foreach ($ups as $up) {
            $up->module = 'navigationController';
            $db->updateObject($up,'userpermission',null,'uid');
        }

//        // rename section table sef_name field to 2.0 standard of sef_url
//        $sects = $db->selectObjects('section',1);
//        foreach ($sects as $sect) {
//            if (empty($sect->sef_url) && !empty($sect->sef_name)) {
//                $sect->sef_url = $sect->sef_name;
//                $db->updateObject($sect,'section');
//            }
//        }
//        $db->sql('ALTER TABLE '.DB_TABLE_PREFIX.'_section DROP sef_name');

		// convert each navigationmodule to a navigation Controller
	    $modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%navigationmodule%'");
	    foreach ($cns as $cn) {
		    $cloc = expUnserialize($cn->internal);
	        $cloc->mod = 'navigationController';
		    $cn->internal = serialize($cloc);
            if ($cn->view == 'Breadcrumb') {
                $cn->action = 'breadcrumb';
                $cn->view = 'breadcrumb';
            } else {
                $cn->action = 'showall';
		        $cn->view = 'showall_'.$cn->view;
            }
	        $db->updateObject($cn,'container');
	        $modules_converted += 1;
	    }

        // correct sectionref module field to full controller classname
        $srs = $db->selectObjects('sectionref','1');
   	    foreach ($srs as $sr) {
            if (expModules::controllerExists($sr->module)) {
                $sr->module = expModules::getControllerClassName($sr->module);
                $db->updateObject($sr,'sectionref');
            }
   	    }

		// delete section_template table
		$db->dropTable('section_template');

        // need to activate new Navigation module modstate if old one was active, leave old one intact
        $ms = $db->selectObject('modstate',"module='navigationmodule'");
        if (!empty($ms) && !$db->selectObject('modstate',"module='navigationController'")) {
            $ms->module = 'navigationController';
            $db->insertObject($ms,'modstate');
        }

        // delete old navigationmodule assoc files (moved or deleted)
        $oldfiles = array (
            'framework/core/definitions/section.php',
            'framework/core/definitions/sectionref.php',
            'framework/core/definitions/section_template.php',
            'framework/modules/file/definitions/file.php',
            'framework/core/models-1/section.php',
            'framework/core/models-1/section_template.php',
            'framework/modules-1/navigationmodule/nav.php',
        );
		// check if the old file exists and remove it
        foreach ($oldfiles as $file) {
            if (file_exists(BASE.$file)) {
                unlink(BASE.$file);
            }
        }
		// delete old navigationmodule, common, & editor connect files
        $olddirs = array(
            "framework/modules-1/navigationmodule/actions/",
            "framework/modules-1/navigationmodule/views/",
        );
        foreach ($olddirs as $dir) {
            if (expUtil::isReallyWritable(BASE.$dir)) {
                expFile::removeDirectory(BASE.$dir);
            }
        }
//        if (expUtil::isReallyWritable(BASE."framework/modules-1/common/views/")) {
//            expFile::removeFilesInDirectory(BASE."framework/modules-1/common/views/");
//        }

		return ($modules_converted?$modules_converted:gt('No'))." ".gt("Navigation modules were upgraded.")."<br>".gt("and navigationmodule files were then deleted.");
	}
}

?>
