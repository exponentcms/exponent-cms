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
class update_profile_paths extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.4';  // code was corrected in 2.0.4

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Corrects bad user profile extension path entries"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.4, the User profile extension table contained full paths which prevented moving Exponent to a new folder after a test install.
	   There was also an issue with the default user avatar path entry.  This Script replaces bad entries with correct ones"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // we'll just do it ine very instance instead of testing if user profile extensions are active
	}

	/**
	 * converts avatar paths that were stored incorrectly
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $fixed = 0;
		// update each bad default avatar reference to default
	    $badavatarurls = $db->selectObjects('user_avatar',"image = ''URL_FULL .'");
	    foreach ($badavatarurls as $badavatarurl) {
            $badavatarurl->image = PATH_RELATIVE.'framework/modules/users/assets/images/avatar_not_found.jpg';
		    $db->updateObject($badavatarurl,'user_avatar');
            $fixed =+1 ;
	    }

		// convert each active user profile extension path from a full to relative path
        $extdirs = array(
            'framework/modules/users/extensions',
            'themes/'.DISPLAY_THEME.'framework/modules/users/extensions'
        );
        foreach ($extdirs as $dir) {
            if (is_readable(BASE.$dir)) {
                $dh = opendir(BASE.$dir);
                while (($file = readdir($dh)) !== false) {
                    if (is_file(BASE."$dir/$file") && is_readable(BASE."$dir/$file") && substr($file, 0, 1) != '_' && substr($file, 0, 1) != '.') {
                        $classname = substr($file,0,-4);
                        $extension = $db->selectObject('profileextension', "classname='".$classname."'");
                        if (!empty($extension->id)) {
                            $extension->classfile = "$dir/$file";
                            $db->updateObject($extension,'profileextension');
                            $fixed =+1 ;
                        }
                    }
                }
            }
        }

        return ($fixed?$fixed:gt('No')).' '.gt('User Profile Extension Paths Corrected');
	}
}

?>
