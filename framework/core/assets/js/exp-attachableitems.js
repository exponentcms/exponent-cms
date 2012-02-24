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

var EXPONENT.attFiles =  function(name) {
    //console.debug(name);
    YAHOO.util.Event.on('addfiles-'+o.name, 'click', function (e){
        YAHOO.util.Event.stopEvent(e);
        win = window.open(eXp.URL_FULL+'framework/modules-1/filemanagermodule/actions/manager.php?update=".$name."', 'IMAGE_BROWSER','left=20,top=20,scrollbars=yes,width=800,height=500,toolbar=0,resizable=0,status=0');
        if (!win) {
            //Catch the popup blocker
            alert('Please disable your popup blocker!!');
        }

        YAHOO.namespace('pagetalk');

        YAHOO.pagetalk.passBackFile".$name." = function(id) {
            //console.debug(id);

            var ej = new EXPONENT.AjaxEvent();
            ej.subscribe(function (o) {
                //console.debug(0);
            },this);
            ej.fetch({action:'getFile',controller:'expFileController',json:1,params:'&id='+id});




            var df = YAHOO.util.Dom.get('filelist".$name."');


            //df.innerHTML = df.innerHTML+ html;
        }

    });

    YAHOO.util.Event.on('filelist".$name."', 'click', function(e){
        YAHOO.util.Event.stopEvent(e);
        var targ = YAHOO.util.Event.getTarget(e);
        while (targ.id != 'displayfiles-".$name."') {
            if(YAHOO.util.Dom.hasClass(targ, 'deletelinks') != false) {
                var dtop = YAHOO.util.Dom.get('filelist".$name."');
                var drem = YAHOO.util.Dom.get(targ.rel);
                dtop.removeChild(drem);
                break;
            } else {
                targ = targ.parentNode;
            }
        }
    });
    
}