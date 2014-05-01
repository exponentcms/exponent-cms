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
 * This is the class update_page_ranks
 */
class update_page_ranks extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.2.1';  // need to use the new 2.0 index start of 1 instead of 0 for ranking

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update page and form control ranks to conform to 2.0 sequence"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In old school code indexes began at 0, now begin at 1.  This script updates page and forms control ranks to the 2.0 format."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        $oldranks = $db->selectObject('section','rank=0 AND parent!=-1');
        if (empty($oldranks)) $oldranks = $db->selectObject('forms_control','rank=0');
        return !empty($oldranks);
	}

	/**
	 * Reranks pages/form controls with index start of 1
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        // adjust page ranks
        self::re_rank(0);  // begin with top level pages
        // now update standalone pages to rank of 0
        foreach ($db->selectObjects('section','parent=-1') as $spg) {
            $spg->rank = 0;
            $db->updateObject($spg,'section');
        }

        // adjust forms control ranks
        foreach ($db->selectObjects('forms',1) as $form) {
            $rank = 1; // 2.0 index starts at 1, not 0 like old school
            foreach ($db->selectObjects('forms_control','forms_id='.$form->id,'rank') as $fc) {
                $fc->rank = $rank++;
                $db->updateObject($fc,'forms_control');
            }
        }

        return gt('Page and Form Control ranks were updated to 2.0 format.');
	}

    function re_rank($parent) {
        global $db;

        $rank = 1; // 2.0 index starts at 1, not 0 like old school
        foreach ($db->selectObjects('section','parent='.$parent,'rank') as $pg) {
            $pg->rank = $rank++;
            $db->updateObject($pg,'section');
            self::re_rank($pg->id);
        }
    }

}

?>
