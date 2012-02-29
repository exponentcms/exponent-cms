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
 * This is the class update_profile_paths
 */
class fix_photo_module extends upgradescript {
	protected $from_version = '1.99.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.5';  // permissions names were changed in 2.0.5

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	function name() { return "Update photoalbum module with correct spelling"; }

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
		return true;  // we'll just do it ine very instance instead of testing if user profile extensions are active
	}

	/**
	 * coverts all headline modules/items into text modules/items and deletes headline controller files
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $count = 0;
        foreach ($db->selectObjects('sectionref',"module='photosController'") as $sr) {
            $sr->module = 'photoController';
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
            $loc->mod = 'photoController';
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
        foreach ($db->selectObjects('userpermission',"module='photosController'") as $up) {
            $up->module = 'photoController';
            $db->updateObject($up,'userpermission',null,'uid');
            $count++;
	    }
        foreach ($db->selectObjects('grouppermission',"module='photosController'") as $gp) {
            $gp->module = 'photoController';
            $db->updateObject($gp,'grouppermission',null,'gid');
            $count++;
	    }
        $ms = $db->selectObject('modstate',"module='photosController'");
        if (!empty($ms)) {
            $ms->module = 'photoController';
            $db->updateObject($ms,'modstate',"module='photosController'",'module');
            $count++;
        }
        return $count.' '.gt('old photoalbum references now have the correct spelling.');
	}
}

?>
