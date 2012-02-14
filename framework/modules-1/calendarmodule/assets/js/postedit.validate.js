/*
 * Copyright (c) 2004-2012 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 */

function validate(frm) {

	if (frm.title.value == "") {
		frm.title.focus();
		alert("You must enter a title");
		return false;
	}

	// Validate dates/times

	
	//alert("This is an event.  Testing date/time stuff");
	
	var startTS = Date.parse(frm.eventstart_hidden.value);
	var endTS = Date.parse(frm.eventend_hidden.value);
	
	if (startTS > endTS) {
		alert("Specified Event Start Date is after the Event's End Date");
		return false;
	}
	
	var startDate = new Date();
	startDate.setTime(startTS); // WILL NOT WORK IN IE
	var endDate = new Date();
	endDate.setTime(endTS); // WILL NOT WORK IN IE
	
	if (startDate.getDate() == endDate.getDate() && startDate.getMonth() == endDate.getMonth() && startDate.getYear() == endDate.getYear()) {
		// dates match
	} else {
		alert("The Event Dates you entered do not match.  Events that span multiple days are not supported.");
		return false;
	}
	
	return true;
}