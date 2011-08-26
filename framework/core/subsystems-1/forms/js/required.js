/*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

g_rgName = new Array();

function trim(s) {
	while (s.charAt(0) == " ") {
		s = s.substr(1);
	}
	return s;
}

function registerRG(name) {
	g_rgName[g_rgName.length] = name;
}

function unregisterRG(name) {
	for (var x = 0; x < g_rgName.length; x++) {
		if (g_rgName[x] == name) {
			g_rgName = "";
		}
	}
}

function checkRG() {
	for (var x = 0; x < g_rgName.length; x++) {
		if (g_rgName[x] != "") {
			alert("Missing required selection for " + decodeURIComponent(g_rgName[x]));
			return false;
		}
	}
	return true;
}

function checkRequired(locForm) {
  for (field in locForm.elements) {
	if (locForm.elements[field]) {
		if (locForm.elements[field].getAttribute) {
			s = locForm.elements[field].getAttribute("required");
			if (s != null) {
				val = trim(locForm.elements[field].value);
				s = decodeURIComponent(s);
				//if ((s == val) || (val == "")) {
				if (locForm.elements[field].type == 'checkbox' || locForm.elements[field].type == 'radio') {
					if (!locForm.elements[field].checked) {
						locForm.elements[field].focus();
                        alert(decodeURIComponent(locForm.elements[field].getAttribute("caption")) + " is a required field.");
                        return false;
					}
					for (var i=0; i<document.form.group1.length; i++)  {
                        if (document.form.group1[i].checked)  {
                            found_it = document.form.group1[i].value //set found_it equal to checked button's value
                        } 
                    }       
				} else {
				    //alert (locForm.elements[field].type);				    
					if (val == "") {
						locForm.elements[field].focus();
						alert(decodeURIComponent(locForm.elements[field].getAttribute("caption")) + " is a required field.");
						return false;
					}
				}
			}
			if (locForm.elements[field].name == "checker" && locForm.elements[field].checked && locForm.elements[field].value != 0 ) {
				alert ("You must choose the correct answer in the anti-spam question to proceed");
				return false;
			}
		}
	}
  }

  return checkRG();

}

