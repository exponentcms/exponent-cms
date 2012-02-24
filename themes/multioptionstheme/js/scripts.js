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

// Namespacing

var Event = YAHOO.util.Event;
var Connection = YAHOO.util.Connect;
var Dom = YAHOO.util.Dom;

//Generic AJAX function
function doIt(url, dest, method, form) {
	if ( method == null ) {
		method = "GET";
	}
	if (form != null) {
		Connection.setForm(Dom.get(form));
	}
   //Send this puppy via asyncRequest
    Connection.asyncRequest(method, url, {
    	success : function(o){
			Dom.get(dest).style.display = 'block';
			Dom.get(dest).innerHTML = o.responseText;
	},
		failure : function(o){
        //	alert('Error handling request: '+o.responseText);
	},
		timeout : 5000
    });
	return false;
}
