<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * This is the class remove_global_categories
 */
class remove_global_categories extends upgradescript {
	protected $from_version = '2.0.5';  // categories were introduced in version, 2.0.5
	protected $to_version = '2.0.8';  // global categories were removed in 2.0.8

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Convert global categories"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.8 global categories were allowed.  This script creates module specific categories for any global categories used, and then deletes global categories."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        return $db->selectObjects('expCats',"module=''") != null ? true : false;
	}

	/**
	 * converts all global categories to a module specific category if used, then delete the global category
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $count = 0;
        $modcount = 0;
        foreach ($db->selectObjects('expCats',"module=''") as $globalcat) {
            $globalcatid = $globalcat->id;
            foreach ($db->selectObjects('content_expCats',"expcats_id=".$globalcat->id) as $catitem) {
                $cat = $db->selectObject('expCats',"title='".$globalcat->title."' AND module='".$catitem->content_type."'");
                if (empty($cat)) {
                    unset ($globalcat->id);
                    $globalcat->module = $catitem->content_type;  // create a module cat like global cat
                    $cat = new stdClass();
                    $cat->id = $db->insertObject($globalcat,'expCats');
                    $modcount++;
                }
                $catitem->expcats_id = $cat->id;  // update the item link to module category
                $db->updateObject($catitem,'content_expCats','expcats_id='.$globalcatid.' AND content_id='.$catitem->content_id);
            }
            $db->delete('expCats',"id=".$globalcatid);  // delete global cat
            $count++;
	    }

        return ($count?$count:gt('No')).' '.gt('global categories converted to').' '.$modcount.' '.gt('module categories, then deleted.');
	}
}

?>
