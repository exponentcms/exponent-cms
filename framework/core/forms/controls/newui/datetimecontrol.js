/*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

function exponent_forms_disable_datetime(id,frm,disable) {
	var elem = document.getElementById("__"+id);
	var status = elem.value;
	
	var ctl_elems = new Array();

	if (status[0] == "1") {
		ctl_elems.push(document.getElementById(id+"_month"));
		ctl_elems.push(document.getElementById(id+"_day"));
		ctl_elems.push(document.getElementById(id+"_year"));
	}
	if (status[1] == "1") {
		ctl_elems.push(document.getElementById(id+"_hour"));
		ctl_elems.push(document.getElementById(id+"_minute"));
		ctl_elems.push(document.getElementById(id+"_ampm"));
	}
	
	for (var i = 0; i < ctl_elems.length; i++) {
		if (disable) ctl_elems[i].setAttribute("disabled","disabled");
		else ctl_elems[i].removeAttribute("disabled");
	}
}