<?php
##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * Smarty {yuimenu} function plugin
 *
 * Type:     function<br>
 * Name:     yuimenu<br>
 * Purpose:  display a yui menu
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_yuimenu($params,&$smarty) {
//FIXME convert to yui3
	$menu = '
        function buildmenu () {
            var oMenuSidenavJs = new YAHOO.widget.Menu("'.$params['buildon'].'", {
                position: "static",
                hidedelay:	100,
                lazyload: true
            });
            var aSubmenuData = '.section::navtojson().';
            oMenuSidenavJs.subscribe("beforeRender", function () {
                if (this.getRoot() == this) {
					for (i=0; i<=this.getItems().length; i++){
						var j=i;
						//  Y.log(aSubmenuData[j].itemdata.length);
						if (aSubmenuData[j].itemdata.length>0){
		                    this.getItem(i).cfg.setProperty("submenu", aSubmenuData[j]);
						}
					}
                }
            });
            oMenuSidenavJs.render();
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