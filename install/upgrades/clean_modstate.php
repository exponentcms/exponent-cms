<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * This is the class clean_modstate
 */
class clean_modstate extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.2.0';  // containermodule was upgraded to containerController in 2.2.0
    public $priority = 98; // set this to a low priority

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Clean out duplicate or obsolete entries in Active Module status table"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Across time, the modstate table can contain duplicate or old school module entries which may cause issues.  This script scrubs the modstate table."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true;
	}

	/**
	 * reads in and corrects the modstate table, esp. since it has no index and allows duplicate entries
     *   we will assume that all old school modules have been upgraded at this point
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $modstates = $db->selectObjects('modstate','1'); // get modstate table
        $oldcount = count($modstates);
        $db->delete('modstate','1');  // erase modstate table
        foreach ($modstates as $ms) {
            if (expModules::controllerExists($ms->module)) {
                $ms->module = expModules::getModuleName($ms->module);  // convert module name to 2.0 style
                if ($db->selectObject('modstate',"module='".$ms->module."'") == null) {
                    $db->insertObject($ms,'modstate',"module='".$ms->module."'");
//                } else {
//                    $db->updateObject($ms,'modstate');
                }
            }
	    }
        $newcount = $db->countObjects('modstate');
        $count = $oldcount - $newcount;

        return ($count?$count:gt('No')).' '.gt('bad modstate entries were corrected.');
	}
}

?>
