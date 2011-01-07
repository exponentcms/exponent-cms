<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Written and Designed by Phillip Ball
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

function smarty_function_yuimenu($params,&$smarty) {

	$menu = '
        function buildmenu () {
            var oMenuSidenavJs = new YAHOO.widget.Menu("'.$params['buildon'].'", { 
																position: "static", 
																hidedelay:	100, 
																lazyload: true });

            var aSubmenuData = '.navigationmodule::navtojson().';
            oMenuSidenavJs.subscribe("beforeRender", function () {

                if (this.getRoot() == this) {
					for (i=0; i<=this.getItems().length; i++){
						var j=i;
						////console.debug(aSubmenuData[j].itemdata.length);
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
	
	exponent_javascript_toFoot("yuimenu-".$params['buildon'],"menu",$smarty->_tpl_vars[__name],$menu);
	
	
}
	
?>