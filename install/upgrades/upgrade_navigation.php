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
	protected $from_version = '1.99.0';
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
        if (expUtil::isReallyWritable(BASE."framework/modules-1/navigationmodule/actions/")) {
            return true;  // the old files still exist
        } else return false;
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
            if ($cn->view = 'Breadcrumb') {
                $cn->view = 'breadcumb';
                $cn->action = 'breadcumb';
            } else {
		        $cn->view = 'showall_'.$cn->view;
                $cn->action = 'showall';
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

        // delete old files (moved or deleted)
        $oldfiles = array (
            'external/editors/connector/popup.js',
            'external/editors/connector/section_linked.php',
            'framework/core/definitions/section.php',
            'framework/core/definitions/sectionref.php',
            'framework/core/definitions/section_template.php',
            'framework/modules/file/definitions/file.php',
            'framework/core/models-1/section.php',
            'framework/core/models-1/section_template.php',
            'framework/modules-1/navigationmodule/nav.php',
            'framework/modules-1/containermodule/actions/copy_to_clipboard.php',
            'framework/modules-1/containermodule/actions/orphans_modules.php',
            'framework/modules-1/containermodule/actions/view_clipboard.php',
            'framework/modules-1/containermodule/actions/view-recycle-bin.php',
            'framework/modules-1/containermodule/views/_orphans_modules.tpl',
            'framework/modules-1/containermodule/views/_view_clipboard.tpl',
            'framework/modules-1/common//views/_msg_queue.tpl',
        );
		// check if the old file exists and remove it
        foreach ($oldfiles as $file) {
            if (file_exists(BASE.$file)) {
                unlink(BASE.$file);
            }
        }
		// delete old navigationmodule, common, & editor connect files
        if (expUtil::isReallyWritable(BASE."framework/modules-1/navigationmodule/actions/")) {
            expFile::removeDirectory(BASE."framework/modules-1/navigationmodule/actions/");
        }
        if (expUtil::isReallyWritable(BASE."framework/modules-1/navigationmodule/views/")) {
            expFile::removeDirectory(BASE."framework/modules-1/navigationmodule/views/");
        }
        if (expUtil::isReallyWritable(BASE."external/editors/connector/lang/")) {
            expFile::removeDirectory(BASE."external/editors/connector/lang/");
        }
//        if (expUtil::isReallyWritable(BASE."framework/modules-1/common/views/")) {
//            expFile::removeFilesInDirectory(BASE."framework/modules-1/common/views/");
//        }

		return ($modules_converted?$modules_converted:gt('No'))." ".gt("Navigation modules were upgraded.")."<br>".gt("and mavigationmodule files were then deleted.");
	}
}

?>
