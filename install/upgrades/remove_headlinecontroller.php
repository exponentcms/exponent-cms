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

/**
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class remove_headlinecontroller
 */
class remove_headlinecontroller extends upgradescript {
	protected $from_version = '1.99.0';
	protected $to_version = '2.0.1';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	function name() { return "Remove the (deprecated) Headline Controller"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Beginning with Exponent 2.0.0 Beta4, the Headline Controller is replaced by the Text Controller.  This Script converts headline modules to text modules and then deletes the headlines"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return false;  // TODO this line is set to false to disable this sample script
	}

	/**
	 * coverts all headline modules/items into text modules/items and deletes headline controller files
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each headline module reference to a text module reference
	    $srs = $db->selectObjects('sectionref',"module = 'headlineController'");
	    foreach ($srs as $sr) {
		    $sr->module = 'textController';
		    $db->updateObject($sr,'sectionref');
	    }
		$lrs = $db->selectObjects('locationref',"module = 'headlineController'");
	    foreach ($lrs as $lr) {
		    $lr->module = 'textController';
		    $db->updateObject($lr,'locationref');
	    }
	    $gps = $db->selectObjects('grouppermission',"module = 'headlineController'");
        foreach ($gps as $gp) {
	        $gp->module = 'textController';
	        $db->updateObject($gp,'grouppermission');
        }
        $ups = $db->selectObjects('userpermission',"module = 'headlineController'");
        foreach ($ups as $up) {
            $up->module = 'textController';
            $db->updateObject($up,'userpermission');
        }

		// convert each headline module to a text module
	    $modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%headlineController%'");
	    foreach ($cns as $cn) {
		    $cloc = expUnserialize($cn->internal);
	        $cloc->mod = 'textController';
		    $cn->internal = serialize($cloc);
		    $cn->view = 'showall';
		    $cn->action = 'showall';
	        $db->updateObject($cn,'container');
	        $modules_converted += 1;
	    }

		// create a text item for each headline item
	    $headlines_converted = 0;
		$headlines = $db->selectObjects('headline',"1");
		foreach ($headlines as $hl) {
			$text = new text();
			$loc = expUnserialize($hl->location_data);
			$loc->mod = "text";
			$text->location_data = serialize($loc);
			$text->title = $hl->title;
			$text->poster = $hl->poster;
			$text->save();
			$text->created_at = $hl->created_at;
            $text->edited_at = $hl->edited_at;
			$text->update();
			$headlines_converted += 1;
		}

		// delete headline table
		$db->dropTable('headline');

		// check if the headline controller files are there and remove them
		$files = array(
		    BASE."framework/core/database/definitions/headline.php",
		    BASE."framework/datatypes/headline.php",
		    BASE."framework/modules/headline/"
		);

        // delete the files.
        $removed = 0;
        $errors = 0;
		foreach ($files as $file) {
		    if (expUtil::isReallyWritable($file)) {
		        unlink ($file);
		        $removed += 1;
		    } else {
		        $errors += 1;
		    }
		} 
		
		return $modules_converted." Headline modules were converted.<br>".$headlines_converted." Headlines were converted.<br>".$removed." files were deleted.<br>".$errors." files could not be removed.";
		
	}
}

?>
