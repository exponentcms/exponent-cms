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

function percent_filter_class() {
	
	this.on_key_press = function(ptObject, evt) {
		evt = (evt) ? evt : event;
		sChar = (evt.charCode) ? evt.charCode : evt.keyCode;
		
		//This will allow backspace to work.
		for (var n =0; n < g_aIgnore.length; n++) {
			if (sChar == g_aIgnore[n]) return true;
		}
		var strNewVal = GetResultingValue(ptObject, String.fromCharCode(sChar));
		
		if (this.isValueIllegal(strNewVal)) {
			return false;
		}
		return true;
	}
	
	this.onblur = function(ptObject) {
		ptObject.value = this.FormatPercent(ptObject.value, true);
		if (ptObject.value != ptObject.previousValue) ptObject.fireEvent("onchange");
	}
	
	this.onfocus = function(ptObject) {
		this.previousValue = ptObject.value
	}
	
	this.onpaste = function(ptObject, evt) {
		var strNewVal = GetResultingValue(ptObject, String.fromCharCode(evt.charCode));
		alert(strNewVal);
		if (this.isValueIllegal(strNewVal)) {
			return false;
		}
		return true;
	}
	
	this.isValueIllegal = function(strValue) {
		var bIsIllegal = false;
		
		if (strValue.match(/\%.*\%/) != null) bIsIllegal = true;
		else if (strValue.match(/\%.+/) != null) bIsIllegal = true;
		else if (strValue.match(/\..*\./) != null) bIsIllegal = true;
		else if (parseInt(strValue) > 999) bIsIllegal = true;
		else if (strValue.match(/\.+\d{5}/) != null) bIsIllegal = true;
		else if (IsNotNumber(strValue.replace("%", "").replace(" ", "")) == true) bIsIllegal = true;
		
		return bIsIllegal;
	}
	
	this.FormatPercent = function(strValue, bIncludeDP) {
		strValue = strValue.replace(/\%/g, "");
		if (strValue.length != 0) {
			while (strValue.charAt(0) == "0") {
				strValue = strValue.substr(1);
			}
			if (strValue.length == 0) strValue = "0";
			var iDP = strValue.length - strValue.indexOf(".");
			if (iDP == strValue.length) strValue = "0" + strValue;
			if (iDP > strValue.length) strValue += ".00";
			else if (iDP == 1) strValue += "00";
			else if (iDP == 2) strValue += "0";
			else if ((iDP > 2) && (iDP < strValue.length)) strValue =  strValue.substr(0,strValue.length - iDP+5);
			
			// Ensure number is postfixed
			strValue = strValue + " %";
		}
		return strValue;
	}
}

var percent_filter = new percent_filter_class();
