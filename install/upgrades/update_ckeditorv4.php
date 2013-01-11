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
 * This is the class update_ckeditorv4
 */
class update_ckeditorv4 extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.1.1';  // ckeditor v4 was added in 2.1.1 and has a different skin

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Updates ckeditor WYSIWYG configurations to use a valid v4 skin"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.1.1, we began using CKEditor v4 which crashes if trying to use a missing skin.  This Script updates those entries"; }

    /**
   	 * additional test(s) to see if upgrade script should be run
   	 * @return bool
   	 */
   	function needed() {
           global $db;

           $settings = $db->selectObject('htmleditor_ckeditor', 'skin!="kama"');
           if (!empty($settings)) {
               return true;
           } else return false;
   	}

   	/**
   	 * updates invalid ckeditor configuration settings
   	 * @return bool
   	 */
   	function upgrade() {
           global $db;

           $fixed = 0;
           $settings = $db->selectObject('htmleditor_ckeditor','skin!="kama"');  // only valid v3 skin still in v4 is 'kama'
           if (!empty($settings)) foreach ($settings as $setting) {
               $setting->skin = 'moono';  // default skin for v4
               $db->updateObject($setting,'htmleditor_ckeditor');
               $fixed++;
           }
           return ($fixed?$fixed:gt('No')).' '.gt('CKEditor configuration settings were corrected');
   	}

}

?>
