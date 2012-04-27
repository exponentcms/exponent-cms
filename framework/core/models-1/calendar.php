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
/** @define "BASE" "../../.." */

class calendar {
	static function form($object) {
		global $user;

		$form = new form();
		if (!isset($object->id)) {
			$object->title = '';
			$object->body = '';
			$object->eventdate = null;
			$object->eventdate->id = 0;
			$object->eventdate->date = time();
			$object->eventstart = time();
			$object->eventend = time()+3600;
			$object->is_allday = 0;
			$object->is_featured = 0;
			$object->is_recurring = 0;
		} else {
			$form->meta('id',$object->id);
		}

		$form->register('title',gt('Title'),new textcontrol($object->title));
		$form->register('body',gt('Body'),new htmleditorcontrol($object->body));

		$form->register(null,'', new htmlcontrol('<hr size="1" />'));

		if ($object->is_recurring == 1) {
			$form->register(null,'',new htmlcontrol(gt('Warning: If you change the date below, it will only affect this specific events.  All other changes can be applied to this and other events.'),false));
		}
		//$form->register('eventdate',gt('Event Date'),new popupdatetimecontrol($object->eventdate->date,'',false));
		$form->register('eventdate',gt('Event Date'),new yuicalendarcontrol($object->eventdate->date,'',false));

		$cb = new checkboxcontrol($object->is_allday,false);
		$cb->jsHooks = array('onclick'=>'exponent_forms_disable_datetime(\'eventstart\',this.form,this.checked); exponent_forms_disable_datetime(\'eventend\',this.form,this.checked);');
		$form->register('is_allday',gt('All Day Event'),$cb);
		$form->register('eventstart',gt('Start Time'),new datetimecontrol($object->eventstart,false));
		$form->register('eventend',gt('End Time'),new datetimecontrol($object->eventend,false));

		if (!isset($object->id)) {
//			$customctl = file_get_contents(BASE.'framework/modules-1/calendarmodule/form.part');
            $custom =  new formtemplate('forms/calendar', '_recurring');
            $customctl = $custom->render();
			//$datectl = new popupdatetimecontrol($object->eventstart+365*86400,'',false);
			$datectl = new yuicalendarcontrol($object->eventdate->date+365*86400,'',false);
			$customctl = str_replace('%%UNTILDATEPICKER%%',$datectl->controlToHTML('untildate'),$customctl);
			$form->register('recur',gt('Recurrence'),new customcontrol($customctl));
		} else if ($object->is_recurring == 1) {
			// Edit applies to one or more...
			$template = new template('calendarmodule','_recur_dates');
			global $db;
			$eventdates = $db->selectObjects('eventdate','event_id='.$object->id.' ORDER BY date');
//			$eventdates = expSorter::sort(array('array'=>$eventdates,'sortby'=>'date', 'order'=>'ASC'));
			if (isset($object->eventdate)) $template->assign('checked_date',$object->eventdate);
			$template->assign('dates',$eventdates);
			$form->register(null,'',new htmlcontrol('<hr size="1"/>'.gt('This event is a recurring event, and occurs on the dates below.  Select which dates you wish to apply these edits to.')));
			$form->register(null,'',new htmlcontrol('<table cellspacing="0" cellpadding="2" width="100%" class="exp-skin-table">'.$template->render().'</table>'));

			$form->meta('date_id',$object->eventdate->id); // Will be 0 if we are creating.
		}

		$form->register('featured_header','',new htmlcontrol('<h3>'.gt('Featured Event Info').'</h3><hr size="1" />'));
		$form->register('is_featured',gt('Feature this event'),new checkboxcontrol($object->is_featured,false));
/* Why was this even here?  No views have it. 6/23/09 Time to implement it - Maia*/
		// $form->register('image_header','',new htmlcontrol('<h3>'.gt('Upload Image File').'</h3><hr size="1" />'));
		// $form->register('file',gt('Upload Image'),new uploadcontrol());

		// $form->register('tag_header','',new htmlcontrol('<h3>'.gt('Tags').'</h3><hr size="1" />'));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));

		return $form;
	}

	static function update($values,$object) {
		$object->title = $values['title'];

		$object->body = preg_replace('/<br ?\/>$/','',trim($values['body']));

		$object->is_allday = (isset($values['is_allday']) ? 1 : 0);
		$object->is_featured = (isset($values['is_featured']) ? 1 : 0);

		$object->eventstart = datetimecontrol::parseData('eventstart',$values);
		$object->eventend = datetimecontrol::parseData('eventend',$values);

		if (!isset($object->id)) {
			global $user;
			$object->poster = $user->id;
			$object->posted = time();
		}

		return $object;
	}
}

?>