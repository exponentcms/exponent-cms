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
 * This is the class upgrade_calendar
 */
class upgrade_calendar extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.1.0';
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Upgrade the Calendar module to a Controller"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Calendar module was upgraded to a Controller in v2.1.0.".
        "This Script converts Calendar modules to the new format and then deletes most old calendarmodule files except those used for backward compatibility."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        return true;
//        if (expUtil::isReallyWritable(BASE."framework/modules-1/navigationmodule/actions/")) {
//            return true;  // the old files still exist
//        } else return false;
   	}

	/**
	 * converts all calendarmodule modules/items into event (controller) modules/items and deletes calendarmodule files
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each calendarmodule reference to an eventController reference
	    $srs = $db->selectObjects('sectionref',"module = 'calendarmodule'");
	    foreach ($srs as $sr) {
		    $sr->module = 'eventController';
		    $db->updateObject($sr,'sectionref');
	    }
	    $gps = $db->selectObjects('grouppermission',"module = 'calendarmodule'");
        foreach ($gps as $gp) {
	        $gp->module = 'eventController';
	        $db->updateObject($gp,'grouppermission',null,'gid');
        }
        $ups = $db->selectObjects('userpermission',"module = 'calendarmodule'");
        foreach ($ups as $up) {
            $up->module = 'eventController';
            $db->updateObject($up,'userpermission',null,'uid');
        }

        $modules_converted = 0;
		// convert each calendarmodule_config to an eventController expConfig
	    $cns = $db->selectObjects('container',"internal LIKE '%calendarmodule%'");
        foreach ($cns as $cn) {
            $oldconfig = $db->selectObject('calendarmodule_config', "location_data='".$cn->internal."'");
   		    $cloc = expUnserialize($cn->internal);
   	        $cloc->mod = 'eventController';
   		    $cn->internal = serialize($cloc);
               if ($cn->view == 'Default') {
                   $cn->action = 'showall';
                   $cn->view = 'showall';
               } else {
                   $cn->action = 'showall';
   		        $cn->view = 'showall_'.$cn->view;
               }
   	        $db->updateObject($cn,'container');

            $newconfig = new expConfig();
            if (!empty($oldconfig)) {
                if ($oldconfig->enable_ical == 1) {
                    $newconfig->config['enable_ical'] = true;
                    $newconfig->config['feed_title'] = $oldconfig->feed_title;
                    $newconfig->config['feed_sef_url'] = $oldconfig->sef_url;
                    $newconfig->config['rss_limit'] = isset($oldconfig->rss_limit) ? $oldconfig->rss_limit : 24;
                    $newconfig->config['rss_cachetime'] = isset($oldconfig->rss_cachetime) ? $oldconfig->rss_cachetime : 1440;
                }
                if (!empty($oldconfig->hidemoduletitle)) {
                    $newconfig->config['hidemoduletitle'] = $oldconfig->hidemoduletitle;
                }
                if (!empty($oldconfig->moduledescription)) {
                    $newconfig->config['moduledescription'] = $oldconfig->moduledescription;
                }
                if (!empty($oldconfig->aggregate) && $oldconfig->aggregate != 'a:0:{}') {
                    $merged = expUnserialize($oldconfig->aggregate);
                    foreach ($merged as $merge) {
                        $newconfig->config['aggregate'][] = $merge;
                    }
                }
                if (!empty($oldconfig->printlink)) {
                    $newconfig->config['printlink'] = $oldconfig->printlink;
                }
                if (!empty($oldconfig->enable_feedback)) {
                    $newconfig->config['enable_feedback'] = $oldconfig->enable_feedback;
                }
                if (!empty($oldconfig->email_title_reminder)) {
                    $newconfig->config['email_title_reminder'] = $oldconfig->email_title_reminder;
                }
                if (!empty($oldconfig->email_from_reminder)) {
                    $newconfig->config['email_from_reminder'] = $oldconfig->email_from_reminder;
                }
                if (!empty($oldconfig->email_address_reminder)) {
                    $newconfig->config['email_address_reminder'] = $oldconfig->email_address_reminder;
                }
                if (!empty($oldconfig->email_reply_reminder)) {
                    $newconfig->config['email_reply_reminder'] = $oldconfig->email_reply_reminder;
                }
                if (!empty($oldconfig->email_showdetail)) {
                    $newconfig->config['email_showdetail'] = $oldconfig->email_showdetail;
                }
                if (!empty($oldconfig->email_signature)) {
                    $newconfig->config['email_signature'] = $oldconfig->email_signature;
                }
                if (empty($oldconfig->enable_tags)) {
                    $newconfig->config['disabletags'] = true;
                }
                if (!empty($oldconfig->enable_categories)) {
                    $newconfig->config['enable_categories'] = $oldconfig->enable_categories;
                }

                // we have to pull in external addresses for reminders
                $addrs = $db->selectObjects('calendar_reminder_address',"calendar_id=".$oldconfig->id);
                foreach ($addrs as $addr) {
                    if (!empty($addr->user_id)) {
                        $newconfig->config['users'][] = $addr->user_id;
                    } elseif (!empty($addr->group_id)) {
                        $newconfig->config['groups'][] = $addr->group_id;
                    } elseif (!empty($addr->email)) {
                        $newconfig->config['addresses'][] = $addr->email;
                    }
                }

                // we have to pull in external calendars for next code
                $extcals = $db->selectObjects('calendar_external',"calendar_id=".$oldconfig->id);
                foreach ($extcals as $extcal) {
                    if ($extcal->type == ICAL_TYPE) {
                        $newconfig->config['pull_ical'][] = $extcal->url;
                    } elseif ($extcal->type == GOOGLE_TYPE) {
                        $newconfig->config['pull_google'][] = $extcal->url;
                    }
                }
            }

            if ($newconfig->config != null) {
                $newmodinternal = expUnserialize($cn->internal);
                $newmod = explode("Controller",$newmodinternal->mod);
                $newmodinternal->mod = $newmod[0];
                $newconfig->location_data = $newmodinternal;
                $newconfig->save();
            }
	        $modules_converted += 1;
	    }

        // convert each eventdate
	    $eds = $db->selectObjects('eventdate',"1");
	    foreach ($eds as $ed) {
		    $cloc = expUnserialize($ed->location_data);
	        $cloc->mod = 'event';
            $ed->location_data = serialize($cloc);
	        $db->updateObject($ed,'eventdate');
	    }

        // convert each calendar to an event
	    $cals = $db->selectObjects('calendar',"1");
	    foreach ($cals as $cal) {
//            $id = $cal['id'];
//            unset($cal['id']);
            unset($cal->approved);
//            $cat = $cal['category_id'];
            unset($cal->category_id);
//            $tags = $cal['tags'];
            unset($cal->tags);
            unset($cal->file_id);
            $loc = expUnserialize($cal->location_data);
            $loc->mod = "event";
            $cal->location_data = serialize($loc);
            $cal->created_at = $cal->posted;
            unset($cal->posted);
            $cal->edited_at = $cal->edited;
            unset($cal->edited);
            $id = $db->insertObject($cal,'event');

            $ev = new event($cal->id);
            $ev->save();

        //FIXME in calendar migration to event conversion,  we have to pull in tags for next code
//        if (!empty($oldconfig->enable_tags)) {
//            $params = null;;
//            $oldtags = expUnserialize($tags);
//            if (!empty($oldtags)) {
//                foreach ($oldtags as $oldtag){
//                    $tagtitle = strtolower(trim($old_db->selectValue('tags','name','id = '.$oldtag)));
//                    $tag = new expTag($tagtitle);
////								$tag->title = $old_db->selectValue('tags','name','id = '.$oldtag);
//                    if (empty($tag->id)) $tag->update(array('title'=>$tagtitle));
//                    $params['expTag'][] = $tag->id;
//                }
//            }
//            $ev->update($params);
//        }

        //FIXME in calendar migration to event conversion, we have to pull in categories for next code
//        if (!empty($oldconfig->enable_categories) && $ri['category_id']) {
//            $params = null;
//            $oldcat = $old_db->selectObject('category','id = '.$ri['category_id']);
//            $cat = new expCat($oldcat->name);
//            if (empty($cat->id)) {
//                $cat->title = $oldcat->name;
//                $cat->color = $oldcat->color;
//                $catloc = expUnserialize($oldcat->location_data);
//                if (array_key_exists($catloc->mod, $this->new_modules)) {
//                    $mod = explode("Controller",$this->new_modules[$catloc->mod]);
//                    $cat->module = $mod[0];
//                }
//                $cat->save();
//                $cat->rank = $oldcat->rank +1;
//                $cat->update();
//            }
//            $params['expCat'][] = $cat->id;
//            $filedownload->update($params);
//        }

	    }

        // need to activate new event module modstate if old one was active, leave old one intact
        $ms = $db->selectObject('modstate',"module='calendarmodule'");
        if (!empty($ms) && !$db->selectObject('modstate',"module='eventController'")) {
            $ms->module = 'eventController';
            $db->insertObject($ms,'modstate');
        }

 		// delete calendarmodule tables
        $db->dropTable('calendar');
        $db->dropTable('calendar_reminder_address');
        $db->dropTable('calendar_external');
        $db->dropTable('calendarmodule_config');

        // delete old calendarmodule assoc files (moved or deleted)
        $oldfiles = array (
            'framework/core/definitions/calendar.php',
            'framework/core/definitions/calendar_external.php',
            'framework/core/definitions/calendar_reminder_address.php',
            'framework/core/definitions/calendarmodule_config.php',
            'framework/core/definitions/eventdate.php',
            'framework/core/models-1/calendar.php',
            'framework/core/models-1/calendarmodule_config.php',
        );
		// check if the old file exists and remove it
        foreach ($oldfiles as $file) {
            if (file_exists(BASE.$file)) {
                unlink(BASE.$file);
            }
        }
		// delete old calendarmodule folders
        $olddirs = array(
            "framework/modules-1/calendarmodule/",
        );
        foreach ($olddirs as $dir) {
            if (expUtil::isReallyWritable(BASE.$dir)) {
                expFile::removeDirectory(BASE.$dir);
            }
        }

        // copy custom views to new location
        $src = THEME_ABSOLUTE."/modules/calendarmodule/views/";
        $dst = THEME_ABSOLUTE."/modules/events/views/event/";
        if (expUtil::isReallyWritable($src)) {
            $dir = opendir($src);
            if (!file_exists($dst)) @mkdir($dst);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if (!file_exists($dst . '/showall_' . $file)) copy($src . '/' . $file,$dst . '/showall_' . $file);
                    //FIXME we also need to copy any .form & .config files
                }
            }
            closedir($dir);
        }

		return ($modules_converted?$modules_converted:gt('No'))." ".gt("Calendar modules were upgraded.")."<br>".gt("and calendarmodule files were then deleted.");
	}
}

?>
