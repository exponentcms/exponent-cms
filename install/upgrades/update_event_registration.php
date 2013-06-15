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
 * This is the class update_event_registration
 */
class update_event_registration extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.2.1';  // when we moved to using site form for event registration, we changed data format

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update event registrations to conform to 2.2.0 format"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.2.0 we stored all event registrations in the eventregistration_registrants table and now use forms data tables.  This script moves old registrations to a form."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        $oldregistrants = $db->selectObject('eventregistration_registrants','value LIKE \'%a:3:{s:4:"name";%\'');
        return !empty($oldregistrants);
	}

	/**
	 * Create a generic form, assign to existing events, and move data to form data table
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        // create a site form
        $newform = new forms();
        $newform->title = 'Event Registration';
        $newform->is_saved = true;
        $newform->update();

        // now add the controls to the site form
        $control = new stdClass();
        $control->name = 'name';
        $control->caption = 'Name';
        $control->forms_id = $newform->id;
        $control->data = 'O:11:"textcontrol":15:{s:7:"caption";s:4:"Name";s:11:"placeholder";s:8:"John Doe";s:7:"pattern";s:0:"";s:4:"size";i:0;s:9:"maxlength";i:0;s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:1;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:4:"name";s:11:"description";s:0:"";}';
        $control->rank = 1;
        $control->is_readonly = 0;
        $control->is_static = 0;
        $db->insertObject($control, 'forms_control');
        $control->name = 'phone';
        $control->caption = 'Phone';
        $control->data = 'O:11:"textcontrol":15:{s:7:"caption";s:5:"Phone";s:11:"placeholder";s:14:"(888) 555-1212";s:7:"pattern";s:0:"";s:4:"size";i:0;s:9:"maxlength";i:0;s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:0;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:5:"phone";s:11:"description";s:0:"";}';
        $control->rank = 2;
        $db->insertObject($control, 'forms_control');
        $control->name = 'email';
        $control->caption = 'Email';
        $control->data = 'O:11:"textcontrol":15:{s:7:"caption";s:5:"Email";s:11:"placeholder";s:18:"johndoe@mailer.org";s:7:"pattern";s:0:"";s:4:"size";i:0;s:9:"maxlength";i:0;s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:0;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:5:"email";s:11:"description";s:0:"";}';
        $control->rank = 3;
        $db->insertObject($control, 'forms_control');

        // create/update the forms data table
        $tablename = $newform->updateTable();

        $registrants = $db->selectObjects('eventregistration_registrants','value LIKE \'%a:3:{s:4:"name";%\'');
        $newreg = new stdClass();
        $count = 0;
        foreach ($registrants as $regs) {
            $event = $db->selectObject('eventregistration','id='.$regs->event_id);
            $event->forms_id = $newform->id;
            $event->multi_registrant = true;
            $db->updateObject($event,'eventregistration');
            $product = $db->selectObject('product','product_type="eventregistration" and product_type_id='.$event->id);

            $reg = expUnserialize($regs->value);
            $newreg->name = $reg['name'];
            $newreg->phone = $reg['phone'];
            $newreg->email = $reg['email'];
            $newreg->referrer = $product->id;
            $newreg->timestamp = $regs->registered_date;
            $loc_data = new stdClass();
            $loc_data->order_id = $regs->connector_id;
            $loc_data->event_id = $product->id;
            $newreg->location_data = serialize($loc_data);
            $db->insertObject($newreg,'forms_' . $tablename);
            $count++;
        }
        // delete the old records
        $db->delete('eventregistration_registrants','value LIKE "%a:3:{s:4:"name";%"');

        return ($count?$count:gt('No'))." ".gt('Event Registrations were updated to 2.2.0 format.');
	}

}

?>
