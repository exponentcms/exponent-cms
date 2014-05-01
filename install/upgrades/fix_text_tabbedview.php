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
 * This is the class fix_text_tabbedview
 */
class fix_text_tabbedview extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.2.0';  // text tabview  deprecated in v2.2.0
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove the (deprecated) Text module 'tabview'"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Text module tabview has been replaced by the tabbed view.  This Script converts Text modules with the tabview."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        return true;  // old files still exist
   	}

	/**
	 * converts all text modules with tabview to tabbed
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each text module tabview to tabbed
	    $modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%text%' and view = 'showall_tabview'");
	    foreach ($cns as $cn) {
		    $cloc = expUnserialize($cn->internal);
	        $cloc->mod = 'text';
		    $cn->internal = serialize($cloc);
		    $cn->view = 'showall_tabbed';
	        $db->updateObject($cn,'container');
	        $modules_converted += 1;
	    }

		return ($modules_converted?$modules_converted:gt('No'))." ".gt("Text modules with tabbed view were updated.");
	}
}

?>
