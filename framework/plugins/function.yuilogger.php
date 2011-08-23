<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "../.." */

function smarty_function_yuilogger($params,&$smarty) {
	global $userjsfiles;
		$logger =  "
		
			var loader = new YAHOO.util.YUILoader();
			loader.insert({
			    require: ['fonts','dragdrop','logger'],
			    base: eXp.URL_FULL+'external/yui/build/',

			    onSuccess: function(loader) {

					var skinContainer = document.body.appendChild(document.createElement(\"div\"));
					var myContainer = skinContainer.appendChild(document.createElement(\"div\"));
					YAHOO.util.Dom.addClass(skinContainer,'yui-skin-sam')
					var configs = {top:'10px',right:'10px',width:'50px'};
		            this.myLogReader = new YAHOO.widget.LogReader(myContainer,configs);
					YAHOO.util.Dom.setStyle(skinContainer,'position','absolute')
					YAHOO.util.Dom.setStyle(skinContainer,'top','10px')
					YAHOO.util.Dom.setStyle(skinContainer,'right','10px')
					YAHOO.util.Dom.setStyle(skinContainer,'z-index','999')
			    }
			});

		// 
		// 
		// 
		// var yuiloggerloader = new YAHOO.util.YUILoader();
		// yuiloggerloader.insert({
		//     require: ['fonts','dragdrop','logger'],
		//     base: eXp.URL_FULL+'external/yui/build/',
		// 	
		//     onSuccess: function(yuiloggerloader) {
		//             // Put a LogReader on your page
		// 			var myContainer = document.body.appendChild(document.createElement('div'));
		// 			YAHOO.util.Dom.addClass(myContainer,'yui-skin-sam')
		//          var myLogReader = new YAHOO.widget.LogReader(myContainer);
		//     }
		// });
	";

	$userjsfiles["logger"]["logger"] = $logger;
}

?>