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

var buildmenu = function (elem,cfg) {

    var oMenuBar = new YAHOO.widget.MenuBar(elem, { 
                                            
                                                constraintoviewport:false,
                                                postion:"dynamic",
                                                visible:true,
                                                zIndex:250,
                                                autosubmenudisplay: true, 
                                                hidedelay: 750, 
                                                lazyload: true });

    var aSubmenuData = cfg.menu;
    oMenuBar.subscribe("beforeRender", function () {

        if (this.getRoot() == this) {
            for (var i=0; i<=this.getItems().length; i++){
                var j=i;
                if (aSubmenuData[j].itemdata.length>0){
                    this.getItem(i).cfg.setProperty("submenu", aSubmenuData[j]);
                    var test = this.getItemGroups();
                    //Y.log(test);
                    //this.setItemGroupTitle("Yahoo! PIM", aSubmenuData[j].itemdata);
                }
            }
        }
    });
    //  Y.log(oMenuBar.getItems());
    
    oMenuBar.render();         

};
