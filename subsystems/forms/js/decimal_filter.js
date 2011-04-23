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

function decimal_filter_class() {
	
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
		var iDPPos = ptObject.value.indexOf(".");
		if (iDPPos == -1) return;
		
		var bValueChanged = false;
		
		if (iDPPos == ptObject.value.length -1) {
			ptObject.value = ptObject.value.substr(0, ptObject.value.length -1);
			bValueChanged = true;
		}
		
		if (iDPPos == 0) {
			var dNewValue = "0" + ptObject.value;
			ptObject.value = dNewValue;
			bValueChanged = true;
		}
		
		if (bValueChanged) ptObject.fireEvent("onchange");
	}
	
	this.onfocus = function(ptObject) {
		//Do nothing for decimal
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
		bIsIllegal = IsNotNumber(strValue);
		if (bIsIllegal == false) {
			if (strValue.match(/\..*\./) != null) bIsIllegal = true;
		}
		return bIsIllegal;
	}
}

var decimal_filter = new decimal_filter_class();
