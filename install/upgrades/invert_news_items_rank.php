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
 * This is the class invert_news_items_rank
 */
class invert_news_items_rank extends upgradescript {
	protected $from_version = '1.99.0';
	protected $to_version = '2.0.8';
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Invert the rank order of news items"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.0.8 a manual sort/rank option was added for News items, however they are ranked in reverse order in earlier versions.  This script inverts the rank order of all news items it finds, so it must only be run once."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
   		return true;  // subclasses MUST return true to be run
   	}

	/**
	 * inverts the rank order of all news items
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// locate each news module
        $cns = $db->selectObjects('container',"internal LIKE '%newsController%'");
        $items_converted = 0;
	    foreach ($cns as $cn) {
            $loc = expUnserialize($cn->internal);
            $loc->mod = 'news';
            $news = $db->selectObjects('news',"location_data='".serialize($loc)."'",'rank DESC');
            $total = 1;
            foreach ($news as $ni) {
                $ni->rank = $total++;
                $db->updateObject($ni,'news');
                $items_converted++;
            }
	    }
		return $items_converted." News items were re-ranked.";
	}
}

?>
