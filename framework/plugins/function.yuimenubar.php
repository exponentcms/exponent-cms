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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {yuimenubar} function plugin
 *
 * Type:     function<br>
 * Name:     yuimenubar<br>
 * Purpose:  display a yui menu bar
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_yuimenubar($params,&$smarty) {
	$menu = '
        function buildmenu () {
            var oMenuBar = new YAHOO.widget.MenuBar("'.$params['buildon'].'", {
                constraintoviewport:false,
                postion:"dynamic",
                visible:true,
                zIndex:250,
                autosubmenudisplay: true,
                hidedelay: 750,
                lazyload: true
            });
            var aSubmenuData = '.navigationController::navtojson().';
            oMenuBar.subscribe("beforeRender", function () {
                if (this.getRoot() == this) {
					for (i=0; i<=this.getItems().length; i++){
						var j=i;
						if (aSubmenuData[j].itemdata.length>0){
		                    this.getItem(i).cfg.setProperty("submenu", aSubmenuData[j]);
						}
					}
                }
            });
            oMenuBar.render();
        }
		YAHOO.util.Event.onDOMReady(buildmenu);
    ';
	
	expJavascript::pushToFoot(array(
	    "unique"=>"yuimenubar-".$params['buildon'],
	    "yui2mods"=>"menu",
	    "yui3mods"=>$smarty->getTemplateVars('__name'),
	    "content"=>$menu,
	    "src"=>""
	 ));
	
}
	
?>