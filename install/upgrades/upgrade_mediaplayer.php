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
 * This is the class upgrade_mediaplayer
 */
class upgrade_mediaplayer extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.2.2';  // mediaplayer module was added in v2.2.0
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Upgrade Flowplayer and YouTube modules to the Media Player module"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Media Player module was added in v2.2.0 with HTML5 support not requiring Flash.  This Script converts Flowplayer and YouTube modules to the new module and then deletes old Flowplayer and YouTube module files."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        global $db;

//        return true;
        $needed = $db->countObjects('container',"internal LIKE '%flowplayer%'");
        $needed += $db->countObjects('container',"internal LIKE '%youtube%'");
        $needed += $db->countObjects('content_expFiles',"content_type = 'flowplayer'");
        $needed += $db->countObjects('flowplayer',1);
        $needed += $db->countObjects('youtube',1);
        if ($needed) {
            return true;
        } else return false;
   	}

	/**
	 * Converts all Flowplayer and YouTube modules/items into Media Player modules/items and deletes Flowplayer and YouTube files
     * based on script priority, the permissions, container references, etc... should've already been upgraded to v2.2
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each Flowplayer and YouTube references to a Media Player
	    $srs = $db->selectObjects('sectionref',"module LIKE '%flowplayer%' or module LIKE '%youtube%'");
	    foreach ($srs as $sr) {
		    $sr->module = 'media';
		    $db->updateObject($sr,'sectionref');
	    }
	    $gps = $db->selectObjects('grouppermission',"module LIKE '%flowplayer%'");
        foreach ($gps as $gp) {
	        $gp->module = 'media';
	        $db->updateObject($gp,'grouppermission',"module LIKE '%flowplayer%' AND source = '".$gp->source."' AND permission = '".$gp->permission."'",'gid');
        }
        $gps = $db->selectObjects('grouppermission',"module LIKE '%youtube%'");
        foreach ($gps as $gp) {
   	        $gp->module = 'media';
   	        $db->updateObject($gp,'grouppermission',"module LIKE '%youtube%' AND source = '".$gp->source."' AND permission = '".$gp->permission."'",'gid');
        }
        $ups = $db->selectObjects('userpermission',"module LIKE '%flowplayer%'");
        foreach ($ups as $up) {
            $up->module = 'media';
            $db->updateObject($up,'userpermission',"module LIKE '%flowplayer%' AND source = '".$up->source."' AND permission = '".$up->permission."'",'uid');
        }
        $ups = $db->selectObjects('userpermission',"module LIKE '%youtube%'");
        foreach ($ups as $up) {
            $up->module = 'media';
            $db->updateObject($up,'userpermission',"module LIKE '%youtube%' AND source = '".$up->source."' AND permission = '".$up->permission."'",'uid');
        }

        // need to replace old module modstate with new media module
        $ms = $db->selectObjects('modstate',"(module LIKE '%flowplayer%' or module LIKE '%youtube%') and active = 1");
        $db->delete('modstate',"module LIKE '%flowplayer%' or module LIKE '%youtube%'");
        if (!empty($ms) && !$db->selectObject('modstate',"module='media'")) {
            $ms = new stdClass();
            $ms->module = 'media';
            $ms->active = 1;
            $db->insertObject($ms,'modstate');
        }

		// convert each Flowplayer module and config to a media one
	    $fp_modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%flowplayer%'");
	    foreach ($cns as $cn) {
            $config = $db->selectObject('expConfigs', "location_data='".$cn->internal."'");
            $old_internal = $cn->internal;
		    $cloc = expUnserialize($cn->internal);
	        $cloc->mod = 'media';
		    $cn->internal = serialize($cloc);
		    $cn->view = 'showall';
		    $cn->action = 'showall';
	        $db->updateObject($cn,'container');

            if (!empty($config->config)) {
                $oldconfig = expUnserialize($config->config);
                unset($oldconfig['autoplay']);
                unset($oldconfig['control_mute']);
                unset($oldconfig['video_style']);
                if (!empty($oldconfig)) {
                    $config->config = serialize($oldconfig);
                    $config->location_data = $cn->internal;
                    $db->updateObject($config,'expConfigs',"location_data='".$old_internal."'");
                }
            }

            $fp_modules_converted += 1;
	    }

        // convert each YouTube module and config to a media one
        $yt_width = 200;
        $yt_height = 143;
	    $yt_modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%youtube%'");
	    foreach ($cns as $cn) {
            $config = $db->selectObject('expConfigs', "location_data='".$cn->internal."'");
            $old_internal = $cn->internal;
		    $cloc = expUnserialize($cn->internal);
	        $cloc->mod = 'media';
		    $cn->internal = serialize($cloc);
		    $cn->view = 'showall';
		    $cn->action = 'showall';
	        $db->updateObject($cn,'container');

            if (!empty($config->config)) {
                $oldconfig = expUnserialize($config->config);
                if (!empty($oldconfig['width'])) {
                    $oldconfig['video_width'] = $oldconfig['width'];
                    unset($oldconfig['width']);
                    $yt_width = $oldconfig['video_width'];
                }
                if (!empty($oldconfig['height'])) {
                    $oldconfig['video_height'] = $oldconfig['height'];
                    unset($oldconfig['height']);
                    $yt_height = $oldconfig['video_height'];
                }
                if (!empty($oldconfig)) {
                    $config->config = serialize($oldconfig);
                    $config->location_data = $cn->internal;
                    $db->updateObject($config,'expConfigs',"location_data='".$old_internal."'");
                }
            }

            $yt_modules_converted += 1;
	    }

		// convert flowplayer items
		$videos = $db->selectArrays('flowplayer',"1");
		foreach ($videos as $vi) {
            $old_vid = $vi['id'];
            unset ($vi['id']);
			$media = new media($vi);
			$loc = expUnserialize($vi['location_data']);
			$loc->mod = "media";
            $media->location_data = serialize($loc);
            $media->save();
            $attached = $db->selectObjects('content_expFiles',"content_type = 'flowplayer' and content_id = ".$old_vid);
            foreach ($attached as $attach) {
                $attach->content_type = 'media';
                if ($attach->subtype == 'video') $attach->subtype = 'media';
                $attach->content_id = $media->id;
                $db->updateObject($attach,'content_expFiles',"content_type = 'flowplayer' AND content_id = ".$old_vid);
            }
		}

        // fix some incomplete previous upgrades
        $attached = $db->selectObjects('content_expFiles',"content_type = 'flowplayer' AND subtype = 'video'");
        foreach ($attached as $attach) {
            $attach->content_type = 'media';
            $attach->subtype = 'media';
            $db->updateObject($attach,'content_expFiles',"content_type = 'flowplayer' AND content_id = ".$attach->content_id);
        }

		// convert youtube items
		$videos = $db->selectArrays('youtube',"1");
		foreach ($videos as $vi) {
            unset ($vi['id']);
            $vi['body'] = $vi['description'];
            $vi['width'] = $yt_width;
            $vi['height'] = $yt_height;
            unset($vi['description']);
            $urltmp = substr($vi['embed_code'],strpos($vi['embed_code'],'src="')+5);
            $urlemb = explode('"',$urltmp);
            $url = str_replace('/embed/','/watch?v=',$urlemb[0]);
            $vi['url'] = $url;
			$media = new media($vi);
			$loc = expUnserialize($vi['location_data']);
			$loc->mod = "media";
            $media->location_data = serialize($loc);
            $media->save();
		}

		// delete Flowplayer and YouTube tables
        $db->dropTable('flowplayer');
        $db->dropTable('youtube');

		// delete old Flowplayer and YouTube folders
        $olddirs = array(
            "framework/modules/flowplayer/",
            "framework/modules/youtube/",
        );
        foreach ($olddirs as $dir) {
            if (expUtil::isReallyWritable(BASE.$dir)) {
                expFile::removeDirectory(BASE.$dir);
            }
        }

		return ($fp_modules_converted?$fp_modules_converted:gt('No'))." ".gt("Flowplayer modules and")." ".($yt_modules_converted?$yt_modules_converted:gt('No'))." ".gt("YouTube modules were converted to Media Player modules.")." ".gt("and then Flowplayer and YouTube files were deleted.");
	}
}

?>
