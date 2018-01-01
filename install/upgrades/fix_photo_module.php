<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
 * This is the class fix_photo_module
 */
class fix_photo_module extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.6';  // photoController name was changed in 2.0.6

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update photoalbum module with correct spelling"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.6, the photo album was plural in some cases and singular in others which prevented full integration.  This script updates existing photo albums."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        if (expUtil::isReallyWritable(BASE."framework/modules/photoalbum/views/photos/")) {
    		return true;  // the old views folder still exists
        } else return false;
	}

	/**
	 * converts the photoController to the new naming scheme
	 * @return string
	 */
	function upgrade() {
	    global $db;

        $count = 0;
        foreach ($db->selectObjects('sectionref',"module='photosController'") as $sr) {
            $sr->module = 'photo';
            $db->updateObject($sr,'sectionref');
            $count++;
	    }
        foreach ($db->selectObjects('photo',"location_data LIKE '%photos%'") as $ph) {
            $loc = expUnserialize($ph->location_data);
            $loc->mod = 'photo';
            $ph->location_data = serialize($loc);
            $db->updateObject($ph,'photo');
            $count++;
	    }
        foreach ($db->selectObjects('container',"internal LIKE '%photos%'") as $co) {
            $loc = expUnserialize($co->internal);
            $loc->mod = 'photo';
            $co->internal = serialize($loc);
            $db->updateObject($co,'container');
            $count++;
	    }
        foreach ($db->selectObjects('expConfigs',"location_data LIKE '%photos%'") as $cf) {
            $loc = expUnserialize($cf->location_data);
            $loc->mod = 'photo';
            $cf->location_data = serialize($loc);
            $db->updateObject($cf,'expConfigs');
            $count++;
	    }
        foreach ($db->selectObjects('grouppermission',"module='photosController'") as $gp) {
            $gp->module = 'photo';
            $db->updateObject($gp,'grouppermission',"module = 'photosController' AND source = '".$gp->source."' AND permission = '".$gp->permission."'",'gid');
            $count++;
	    }
        foreach ($db->selectObjects('userpermission',"module='photosController'") as $up) {
            $up->module = 'photo';
            $db->updateObject($up,'userpermission',"module = 'photosController' AND source = '".$up->source."' AND permission = '".$up->permission."'",'uid');
            $count++;
	    }
        $ms = $db->selectObject('modstate',"module='photosController'");
        if (!empty($ms)) {
            $ms->module = 'photo';
            $db->updateObject($ms,'modstate',"module='photosController'",'module');
            $count++;
        }
        return ($count?$count:gt('No')).' '.gt('old photoalbum references had the spelling corrected.');
	}
}

?>
