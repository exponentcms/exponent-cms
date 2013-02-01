/*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

YUI(EXPONENT.YUI3_CONFIG).use('node','event', function(Y) {

    var showMenu = function(e) {
        e.halt();
        hideMenus();
        e.target.addClass('current-trigger').next().setStyles({'display':'block','zIndex':50});
    }
    
    var hideMenus = function(e) {
        Y.all(".container-chrome .container-menu").setStyle('display','none').each(function(n){
            n.previous().removeClass('current-trigger');  
        });
    }

    Y.on('domready',function(){
        var chromes = Y.all(".container-chrome");
        chromes.each(function(n,k){
            n.setStyle('zIndex',(chromes.size()-k)+10);
            //n.one(".exp-dropmenu").setStyle('zIndex',chromes.size()+1);
        });

        if (Y.UA.ie < 9 && Y.UA.ie > 6) {
            Y.delegate('click', showMenu, document.body, '.container-chrome .trigger');
            Y.on('click', hideMenus, document.body);            
        } else {
            Y.on('click', hideMenus, document.body);
            Y.delegate('click', showMenu, document.body, '.container-chrome .trigger');
            
        };

        // move hard coded mod menus inside the mod wrapper they pertain to
        Y.all('.hardcoded-chrome').each(function(n,k){
            n.get('parentNode').next().prepend(n.get('parentNode'));
        });

    });
});
