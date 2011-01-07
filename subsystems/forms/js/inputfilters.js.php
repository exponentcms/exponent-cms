<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Written and Designed by James Hunt
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

?>

var g_aIgnore = Array(8,9,37,38,39,40,35,36,46);

function GetResultingValue(ptObject, strInsert) {
	//IE support
	var strPostValue = "";
	if (document.selection) {
		var trCaret = window.document.selection.createRange();
		var trPrefix = ptObject.createTextRange();
		
		var trSuffix = trPrefix.duplicate();
		
		trPrefix.setEndPoint("EndToStart", trCaret);
		
		trSuffix.setEndPoint("StartToEnd", trCaret);
		strPostValue = trPrefix.text + strInsert + trSuffix.text;
	}
	//MOZILLA/NETSCAPE support
	else if (ptObject.selectionStart || ptObject.selectionStart == '0') {
		var startPos = ptObject.selectionStart;
		var endPos = ptObject.selectionEnd;
		strPostValue = ptObject.value.substring(0, startPos) + strInsert + ptObject.value.substring(endPos, ptObject.value.length);
	} 
	//SAFARI support, 
	//I know this isn't quite right, but if anyone can get it to work let us know!!
	else if (window.getSelection) {
		strPostValue = ptObject.value + strInsert;
	}
	return strPostValue;
}

function IsNotNumber(strValue) {
	if (strValue == ".") return false;
	
	if (isNaN(parseFloat(strValue,10))) return true;
	
	if (strValue.match(/.*[\+\-]/) != null) return true;
	
	if (strValue.match(/[^0123456789\-\+\.]/) != null) return true;
	
	if (strValue.match(/.+\..+\./) != null) return true;
	
	return false;
}




<?php 
	$dh = opendir(".");
	while (($file=readdir($dh)) !== false) {
		if (is_file($file) && substr($file,0,1) != "." && $file != "inputfilters.js.php") {
			include_once($file);
		}
	}
?>
