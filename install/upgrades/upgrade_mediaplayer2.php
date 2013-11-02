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
 * This is the class upgrade_mediaplayer2
 */
class upgrade_mediaplayer2 extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.2.4';  // mediaplayer module was added in v2.2.0
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Upgrade EAAS module YouTube items to the Media Player module"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The YouTube module was deprecated in v2.2.0.  This Script converts EAAS module YouTube items to Media Player items."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        global $db;

//        return true;
        $needed = $db->countObjects('container',"internal LIKE '%eaas%'");
        if ($needed) {
            return true;
        } else return false;
   	}

	/**
	 * Converts all EAAS items to use Media Player module instead of deprecated YouTube module
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each EAAS module config youtube item to a media player one
        // we MUST make an assumption the old youtube modules were converted to media player modules by the upgrade_mediplayer script
	    $yt_modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%eaas%'");
	    foreach ($cns as $cn) {
            $config = new expConfig(expUnserialize($cn->internal));
            if (!empty($config->config)) {
                $newconfig = array();
                $newconfig['config']['expFile']['media_image'] = $config->config['expFile']['youtube_image'];
                $newconfig['config']['youtube_image'] = array();
                if (empty($config->config['media_body']) && !empty($config->config['youtube_body'])) $newconfig['config']['media_body'] = $config->config['youtube_body'];
                $newconfig['config']['youtube_body'] = '';
                if (!empty($config->config['youtube_aggregate'])) $newconfig['config']['media_aggregate'] = array_merge($config->config['media_aggregate'],$config->config['youtube_aggregate']);
                $newconfig['config']['youtube_aggregate'] = '';
                $config->update($newconfig);
            }
            $yt_modules_converted += 1;
	    }

		return ($yt_modules_converted?$yt_modules_converted:gt('No'))." ".gt("EAAS modules were updated to use Media Player.");
	}
}

?>
