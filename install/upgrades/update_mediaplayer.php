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
 * This is the class update_mediaplayer
 */
class update_mediaplayer extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.2.3';  // mediaplayer module table was updated in v2.2.3
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Upgrade Media Player and File Download item to set the media/file type"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Media Player and File Download modules were updated in v2.2.3 by storing the media/file type.  This Script updates Media Player and File Download items to set the appropriate media/file type."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        global $db;

//        return true;
        $needed = $db->countObjects('media',"media_type = ''");
        $needed += $db->countObjects('filedownload',"file_type = ''");
        if ($needed) {
            return true;
        } else return false;
   	}

	/**
	 * Update all Media Player items to set media type
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// update each Media Player module as required
	    $mp_items_converted = 0;
	    $mps = $db->selectObjects('media',"media_type = ''");
	    foreach ($mps as $mp) {
            if (!empty($mp->url)) {
                $mp->media_type = 'youtube';
            } else {
                $mp->media_type = 'file';
            }
	        $db->updateObject($mp,'media');
            $mp_items_converted += 1;
	    }

        // update each File Download module as required
	    $fd_items_converted = 0;
	    $fds = $db->selectObjects('filedownload',"file_type = ''");
	    foreach ($fds as $fd) {
            if (!empty($fd->url)) {
                $fd->file_type = 'ext_file';
            } else {
                $fd->file_type = 'file';
            }
	        $db->updateObject($fd,'filedownload');
            $fd_items_converted += 1;
	    }

		return ($mp_items_converted?$mp_items_converted:gt('No'))." ".gt("Media Player items were updated.") . ' ' . gt('and') . ' ' . ($fd_items_converted?$fd_items_converted:gt('No'))." ".gt("File Download items were updated.");
	}
}

?>
