<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * This is the class fix_forms3
 *
 * @package Installation
 * @subpackage Upgrade
 */
class fix_forms3 extends upgradescript {
	protected $from_version = '2.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.7.1';  // forms controls types updated in 2.7.1

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update saved forms control records with updated type"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Originally most forms controls had a 'text' type.  This script updates existing forms control records."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        // find any forms controls
        if ($db->countObjects('forms_control')) {
            return true;
   	    }

        return false;
	}

	/**
	 * fixes existing forms control records
	 * @return string
	 */
	function upgrade() {
	    global $db;

        // fix any saved tables without sef urls
        $controls_updated = 0;
        foreach ($db->selectObjects('forms_control') as $control) {
            $data = expUnserialize($control->data);
            switch (get_class($data)) {
                case 'buttongroupcontrol':
                    $type = 'button';
                    break;
                case 'calendarcontrol':
                case 'popupdatetimecontrol':
                case 'yuicalendarcontrol':
                case 'yuidatetimecontrol':
                    $type = 'datetime';
                    break;
                case 'checkboxcontrol':
                case 'countrycontrol':
                case 'countryregioncontrol':
                case 'dropdowncontrol':
                case 'listbuildercontrol':
                case 'radiocontrol':
                case 'radiogroupcontrol':
                case 'statescontrol':
                case 'tagpickercontrol':
                case 'tagtreecontrol':
                    $type = 'select';
                    break;
                case 'filemanagercontrol':
                case 'uploadcontrol':
                    $type = 'file';
                    break;
                case 'pagecontrol':
                    $type = 'page';
                    break;
                case 'colorcontrol':
                    $type = 'color';
                    break;
                case 'rangecontrol':
                    $type = 'range';
                    break;
                case 'telcontrol':
                    $type = 'tel';
                    break;
                case 'urlcontrol':
                    $type = 'url';
                    break;
                case 'numbercontrol':
                    $type = 'number';
                    break;
                case 'passwordcontrol':
                    $type = 'password';
                    break;
                case 'emailcontrol':
                    $type = 'email';
                    break;
                default:
                    $type = 'text';
            }
            if ($data->type != $type) {
                $data->type = $type;
                $ctl = new forms_control($control->id);
                $ctl->data = serialize($data);
                $ctl->update();
                $controls_updated++;
            }
	    }

        return ($controls_updated?$controls_updated:gt('No'))." ".gt("Forms controls had their type updated.");
	}
}

?>
