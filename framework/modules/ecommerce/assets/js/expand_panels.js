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

YUI(EXPONENT.YUI3_CONFIG).use('node','cookie','anim', function(Y) {
        var panels = Y.all(".dashboard .panel");
        var expandHeight = [];
        var action = function(e){
            e.halt();

            var pBody = e.target.ancestor('.panel').query('.bd');
            var pID = e.target.ancestor('.panel').getAttribute('id');
            var cfg = {
                node: pBody,
                duration: 0.5,
                easing: Y.Easing.easeOut
            }
            
            if (e.target.getAttribute("class")=="collapse") {
                cfg.to = { height: 0 };
                cfg.from = { height: expandHeight[pID] };
                pBody.setStyle('height',expandHeight[pID]+"px");
                pBody.replaceClass('expanded','collapsed');
                e.target.replaceClass('collapse','expand');
                Y.Cookie.set(pID, "collapsed");
            } else {
                pBody.setStyle('height',0);
                cfg.from = { height: 0 };
                cfg.to = { height: expandHeight[pID] };
                pBody.replaceClass('collapsed','expanded');
                e.target.replaceClass('expand','collapse');
                Y.Cookie.set(pID, "expanded");
            }
            var anim = new Y.Anim(cfg);
            
            anim.run();
        }
        panels.each(function(n,k){
            n.delegate('click',action,'.hd a');
            if (Y.Cookie.get(n.get('id'))==="collapsed") {
                n.query('.hd a').replaceClass('collapse','expand');
                n.query('.bd').addClass('collapsed');
            };
            expandHeight[n.get('id')] = n.query('.bd ul').get('offsetHeight');
        });
    });