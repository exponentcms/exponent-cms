<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * This is the class remove_headlinecontroller
 *
 * @package Installation
 * @subpackage Upgrade
 */
class remove_headlinecontroller extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.2.0';  // headline controller fully deprecated in v2.2.0
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove the (deprecated) Headline Controller"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Headline Controller has been replaced by the Text Controller.  This Script converts Headline modules to Text modules and then deletes the Headline module files."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        if (expUtil::isReallyWritable(BASE."framework/modules/headline/")) {
       		return true;  // old files still exist
        } else return false;
   	}

	/**
	 * converts all headline modules/items into text modules/items and deletes headline controller files
	 * @return string
	 */
	function upgrade() {
	    global $db;

		// convert each headline module reference to a text module reference
	    $srs = $db->selectObjects('sectionref',"module = 'headline'");
	    foreach ($srs as $sr) {
		    $sr->module = 'text';
		    $db->updateObject($sr,'sectionref');
	    }
	    $gps = $db->selectObjects('grouppermission',"module = 'headline'");
        foreach ($gps as $gp) {
	        $gp->module = 'text';
	        $db->updateObject($gp,'grouppermission',"module = 'headline' AND source = '".$gp->source."' AND permission = '".$gp->permission."'",'gid');
        }
        $ups = $db->selectObjects('userpermission',"module = 'headline'");
        foreach ($ups as $up) {
            $up->module = 'text';
            $db->updateObject($up,'userpermission',"module = 'headline' AND source = '".$up->source."' AND permission = '".$up->permission."'",'uid');
        }

		// convert each headline module to a text module
	    $modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%headline%'");
	    foreach ($cns as $cn) {
		    $cloc = expUnserialize($cn->internal);
	        $cloc->mod = 'text';
		    $cn->internal = serialize($cloc);
		    $cn->view = 'showall_headline';
		    $cn->action = 'showall';
	        $db->updateObject($cn,'container');
	        $modules_converted++;
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
			$headlines_converted++;
		}

		// delete headline table
		$db->dropTable('headline');

		// check if the headline controller files are there and remove them
        if (expUtil::isReallyWritable(BASE."framework/modules/headline/")) {
            expFile::removeDirectory(BASE."framework/modules/headline/");
        }

		return ($modules_converted?$modules_converted:gt('No'))." ".gt("Headline modules were converted.")."<br>".($headlines_converted?$headlines_converted:gt('No'))." ".gt("Headlines were converted.")."<br>".gt("and Headline module files were then deleted.");
	}
}

?>
