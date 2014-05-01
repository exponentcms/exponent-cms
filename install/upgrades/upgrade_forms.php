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
 * This is the class upgrade_forms
 */
class upgrade_forms extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.2.0';  // formmodule (formbuilder) will be fully deprecated in v2.1.2
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Upgrade the Forms module to a Controller"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Forms module was upgraded to a Controller in v2.1.1.".
        "This Script converts Forms modules to the new format and then deletes most old formmodule and formbuilder files."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        return true;
   	}

	/**
	 * converts all formmodule modules/items into forms (controller) modules/items and deletes formmodule/formbuilder files
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each formmodule reference to an formsController reference
	    $srs = $db->selectObjects('sectionref',"module = 'formmodule'");
	    foreach ($srs as $sr) {
		    $sr->module = 'forms';
		    $db->updateObject($sr,'sectionref');
	    }
	    $gps = $db->selectObjects('grouppermission',"module = 'formmodule'");
        foreach ($gps as $gp) {
	        $gp->module = 'forms';
	        $db->updateObject($gp,'grouppermission',"module = 'formmodule' AND source = '".$gp->source."' AND permission = '".$gp->permission."'",'gid');
        }
        $ups = $db->selectObjects('userpermission',"module = 'formmodule'");
        foreach ($ups as $up) {
            $up->module = 'forms';
            $db->updateObject($up,'userpermission',"module = 'formmodule' AND source = '".$up->source."' AND permission = '".$up->permission."'",'uid');
        }

        $modules_converted = 0;
		// convert each formbuilder_form & formbuilder_report record to a forms object with an formsController expConfig
	    $cns = $db->selectObjects('container',"internal LIKE '%formmodule%'");
        foreach ($cns as $cn) {
            $oldform = $db->selectObject('formbuilder_form', "location_data='".$cn->internal."'");
            $oldreport = $db->selectObject('formbuilder_report', "location_data='".$cn->internal."'");

            $cloc = expUnserialize($cn->internal);
      	    $cloc->mod = 'forms';
      		$cn->internal = serialize($cloc);
            $cn->action = 'enterdata';
            $cn->view = 'enterdata';
      	    $db->updateObject($cn,'container');

            if (!empty($oldform->id)) {
                // convert form data table by renaming it
                if ($oldform->is_saved) {
                    $i = '';
                    $test_tablename = $oldform->table_name;
                    while ($db->tableExists('forms_'.$test_tablename)) {
                        $i++;
                        $test_tablename = $oldform->table_name . $i;
                    }
                    $oldform_table_name = $oldform->table_name;
                    $oldform->table_name = $oldform_table_name . $i;
                    $db->sql('RENAME TABLE '.DB_TABLE_PREFIX.'_formbuilder_'.$oldform_table_name.' TO '.DB_TABLE_PREFIX.'_forms_'.$oldform->table_name);
                    //FIXME do we want to add a forms_id field?
                }

                $newform = new forms();
                $newform->title = !empty($oldform->name) ? $oldform->name : gt('Untitled');
                $newform->is_saved = $oldform->is_saved;
                $newform->table_name = $oldform->table_name;
                $newform->description = $oldform->description;
                $newform->response = $oldform->response;
                $newform->report_name = $oldreport->name;
                $newform->report_desc = $oldreport->description;
                $newform->report_def = $oldreport->text;
                $newform->column_names_list = $oldreport->column_names;
                $newform->update();

                 // copy & convert each formbuilder_control to a forms_control
                $fcs = $db->selectObjects('formbuilder_control',"form_id=".$oldform->id);
                foreach ($fcs as $fc) {
                    $fc->forms_id = $newform->id;
                    unset ($fc->id);
                    unset ($fc->form_id);
                    $fc->rank++;  // 2.0 index begins at 1
                    $db->insertObject($fc,'forms_control');
                }

                // convert the form & report configs to an expConfig object for this module
                $newconfig = new expConfig();
                $newconfig->config['forms_id'] = $newform->id;
                if (!empty($oldform->name)) $newconfig->config['title'] = $oldform->name;
                if (!empty($oldform->description)) $newconfig->config['description'] = $oldform->description;
                if (!empty($oldform->response)) $newconfig->config['response'] = $oldform->response;
                if (!empty($oldform->is_email)) $newconfig->config['is_email'] = $oldform->is_email;
                if (!empty($oldform->select_email)) $newconfig->config['select_email'] = $oldform->select_email;
                if (!empty($oldform->submitbtn)) $newconfig->config['submitbtn'] = $oldform->submitbtn;
                if (!empty($oldform->resetbtn)) $newconfig->config['resetbtn'] = $oldform->resetbtn;
                if (!empty($oldform->style)) $newconfig->config['style'] = $oldform->style;
                if (!empty($oldform->subject)) $newconfig->config['subject'] = $oldform->subject;
                if (!empty($oldform->is_auto_respond)) $newconfig->config['is_auto_respond'] = $oldform->is_auto_respond;
                if (!empty($oldform->auto_respond_subject)) $newconfig->config['auto_respond_subject'] = $oldform->auto_respond_subject;
                if (!empty($oldform->auto_respond_body)) $newconfig->config['auto_respond_body'] = $oldform->auto_respond_body;
                if (!empty($oldreport->name)) $newconfig->config['report_name'] = $oldreport->name;
                if (!empty($oldreport->description)) $newconfig->config['report_desc'] = $oldreport->description;
                if (!empty($oldreport->text)) $newconfig->config['report_def'] = $oldreport->text;
                if (!empty($oldreport->column_names)) $newconfig->config['column_names_list'] = explode('|!|',$oldreport->column_names);

                // we have to pull in addresses for emails
                $addrs = $db->selectObjects('formbuilder_address',"form_id=".$oldform->id);
                foreach ($addrs as $addr) {
                    if (!empty($addr->user_id)) {
                        $newconfig->config['user_list'][] = $addr->user_id;
                    } elseif (!empty($addr->group_id)) {
                        $newconfig->config['group_list'][] = $addr->group_id;
                    } elseif (!empty($addr->email)) {
                        $newconfig->config['address_list'][] = $addr->email;
                    }
                }

                // now save/attach the expConfig
                if ($newconfig->config != null) {
                    $newmodinternal = expUnserialize($cn->internal);
//                    $newmod = explode("Controller",$newmodinternal->mod);
//                    $newmodinternal->mod = $newmod[0];
                    $newmodinternal->mod = expModules::getModuleName($newmodinternal->mod);
                    $newconfig->location_data = $newmodinternal;
                    $newconfig->save();
                }
                $modules_converted += 1;
            }
	    }

        // need to activate new forms module modstate if old one was active, leave old one intact
        $ms = $db->selectObject('modstate',"module='formmodule'");
        if (!empty($ms) && !$db->selectObject('modstate',"module='formsController'")) {
            $ms->module = 'forms';
            $db->insertObject($ms,'modstate',"module='formmodule'");
        }

 		// delete formmodule tables
        $db->dropTable('formbuilder_address');
        $db->dropTable('formbuilder_control');
        $db->dropTable('formbuilder_form');
        $db->dropTable('formbuilder_report');

        // delete old formmodule assoc files (moved or deleted)
        $oldfiles = array (
            'framework/core/definitions/formbuilder_address.php',
            'framework/core/definitions/formbuilder_control.php',
            'framework/core/definitions/formbuilder_form.php',
            'framework/core/definitions/formbuilder_report.php',
            'framework/core/models-1/formbuilder_form.php',
            'framework/core/models-1/formbuilder_report.php',
        );
		// check if the old file exists and remove it
        foreach ($oldfiles as $file) {
            if (file_exists(BASE.$file)) {
                unlink(BASE.$file);
            }
        }
		// delete old formmodule folders
        $olddirs = array(
            "framework/modules-1/formmodule/",
            "framework/modules-1/formbuilder/",
        );
        foreach ($olddirs as $dir) {
            if (expUtil::isReallyWritable(BASE.$dir)) {
                expFile::removeDirectory(BASE.$dir);
            }
        }

        //FIXME ???
        // copy custom views to new location
//        $src = THEME_ABSOLUTE."modules/formmodule/views";
//        $dst = THEME_ABSOLUTE."modules/forms/views/forms";
//        if (expUtil::isReallyWritable($src)) {
//            $dir = opendir($src);
//            if (!file_exists($dst)) @mkdir($dst,DIR_DEFAULT_MODE_STR,true);
//            while(false !== ( $file = readdir($dir)) ) {
//                if (( $file != '.' ) && ( $file != '..' )) {
//                    if (!file_exists($dst . '/showall_' . $file)) copy($src . '/' . $file,$dst . '/showall_' . $file);
//                }
//            }
//            closedir($dir);
//        }

		return ($modules_converted?$modules_converted:gt('No'))." ".gt("Form modules were upgraded.")."<br>".gt("and formmodule/formbuilder files were then deleted.");
	}
}

?>
