/*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

function money_filter_class() {
	
	this.on_key_press = function(ptObject, evt) {
		
		evt = (evt) ? evt : event;
		sChar = (evt.charCode) ? evt.charCode : evt.keyCode;
		
		for (var n =0; n < g_aIgnore.length; n++) {
			if (sChar == g_aIgnore[n]) return true;
		}
		
		var strOldValue = ptObject.value;
		var strNewValue = GetResultingValue(ptObject, String.fromCharCode(sChar));
		strNewValue = this.FormatUSCurrency(strNewValue, false);
		if (this.isValueIllegal(strNewValue)) return false;
		ptObject.value = strNewValue;
		
		this.SetCaretPosition(strOldValue, strNewValue,ptObject);
	
		return false;
	}
	
	this.onblur = function (ptObject) {
        ptObject.value = this.FormatUSCurrency(ptObject.value, true);
        if (ptObject.value != ptObject.previousValue) {
//            ptObject.fireEvent("change");
            fireEvent(ptObject, "change");
        }
    }
	
	this.onfocus = function(ptObject) {
		this.previousValue = ptObject.value
	}
	
	this.onpaste = function(ptObject, evt) {
		var strNewVal = GetResultingValue(ptObject, String.fromCharCode(evt.charCode));
		alert(strNewVal);
		return !this.isValueIllegal(strNewVal);

	}
	
	this.isValueIllegal = function(strValue) {
		var bIsIllegal = false;
		var temp = strValue.replace(/,/g, "");
		if (strValue.match(/[^s]\$/)) bIsIllegal = true;
		else if (strValue.match(/\..*\./) != null) bIsIllegal = true;
		else if (strValue.match(/\.+\d{3}/) != null) bIsIllegal = true;
		else if (parseInt(temp.substr(1)) > 9999999999) bIsIllegal = true;
		else if (IsNotNumber(strValue.replace(/\$/g, "").replace(/,/g, ""))) bIsIllegal = true;
		
		return bIsIllegal;
	}
	
	
	this.FormatUSCurrency = function(strValue, bIncludeDP) {
		strValue = strValue.replace(/,/g, "");
		
		var iDPPosition = strValue.indexOf(".");
		if (iDPPosition == -1) iDPPosition = strValue.length;
		for (i = iDPPosition -3; i > 0; i -= 3) strValue = strValue.substr(0, i) + "," + strValue.substr(i);
	
		strValue = "$" + strValue.replace(/\$/g, "");
	
		strValue = strValue.replace("$,","$");
		
		if (bIncludeDP) {
			var iDP = strValue.length - strValue.indexOf(".");
			if (iDP > strValue.length) strValue += ".00";
			else if (iDP == 1) strValue += "00";
			else if (iDP == 2) strValue += "0";
			
			if (strValue == "$.00") strValue = "$0.00";
		}
		return strValue;
	}
	
	this.SetCaretPosition = function(strOld, strNew, ptObject) {
		var i = -1;
		strOld = strOld.replace(/,/g, "");
		strOld = strOld.replace(/\$/g, "");
		var strTemp = strNew.replace(/,/g, "");
		strTemp = strTemp.replace(/\$/g, "");
		var newCount = (((strTemp.length - strOld.length)<0)?1:(strTemp.length - strOld.length));
		var iInsertPoint = strNew.length;
		
		for (var x = 0; x < strNew.length; x++) {
			if ((strNew.substr(x,1) != "$") && (strNew.substr(x,1) != ",")) {
				i++;
				if (strNew.substr(x,1) != strOld.substr(i,1)) {
					iInsertPoint = x + newCount;
					break;
				}
			}
		}
		
		if (document.selection) {
			trCaret = ptObject.createTextRange();
			trCaret.collapse(true);
			trCaret.moveStart("character", iInsertPoint);
			trCaret.select();
		}
		else if (ptObject.selectionStart || ptObject.selectionStart == '0') {
			ptObject.selectionStart = iInsertPoint;
			ptObject.selectionEnd = iInsertPoint;
		}		
	}
	
	
}

var money_filter = new money_filter_class();
