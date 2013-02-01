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

EXPONENT.attFiles = function(name) {
    //Y.log(name);
    YAHOO.util.Event.on('addfiles-'+o.name, 'click', function (e){
        YAHOO.util.Event.stopEvent(e);
        win = window.open(eXp.PATH_RELATIVE+'framework/modules-1/filemanagermodule/actions/manager.php?update=".$name."', 'IMAGE_BROWSER','left=20,top=20,scrollbars=yes,width=800,height=500,toolbar=0,resizable=0,status=0');
        if (!win) {
            //Catch the popup blocker
            alert('Please disable your popup blocker!!');
        }

        YAHOO.namespace('pagetalk');

        YAHOO.pagetalk.passBackFile".$name." = function(id) {
            //Y.log(id);

            var ej = new EXPONENT.AjaxEvent();
            ej.subscribe(function (o) {
                //Y.log(0);
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

    	// YUI 2 ajax helper method. This is much easier in YUI 3. Should also migrate.
	EXPONENT.AjaxEvent = function() {
	    var obj;
	    var data = "";

	    var gatherURLInfo = function (obj){
	        json = obj.json ? "&json=1" : "";
	        if (obj.form){
	            data = YAHOO.util.Connect.setForm(obj.form);
	            //slap a date in there so IE doesn't cache
	            var dt = new Date().valueOf();
	            var sUri = EXPONENT.PATH_RELATIVE + "index.php?ajax_action=1" + json + "&yaetime=" + dt;
	            return sUri;
	        } else if (!obj.action || (!obj.controller && !obj.module)) {
	            alert("If you don't pass the ID of a form, you need to specify both a module/controller AND and a corresponding action.");
	        } else {
	            //slap a date in there so IE doesn't cache
	            var dt = new Date().valueOf();
	            var modcontrol = (obj.controller) ? "&controller="+obj.controller : "&module="+obj.module;
	            var sUri = EXPONENT.PATH_RELATIVE + "index.php?ajax_action=1" + modcontrol + "&action=" + obj.action + json + "&yaetime=" + dt + obj.params;
	            return sUri;
	        }
	    }

	    return {
	        json:0,
	        subscribe: function(fn, oScope) {
	            if (!this.oEvent) {
	                this.oEvent = new YAHOO.util.CustomEvent("ajaxevent", this, false, YAHOO.util.CustomEvent.FLAT);
	            }
	            if (oScope) {
	                this.oEvent.subscribe(fn, oScope, true);
	            } else {
	                this.oEvent.subscribe(fn);
	            }
	        },
	        fetch: function(obj) {
	            if (typeof obj == "undefined" || !obj){
	                alert('{/literal}{"EXPONENT.ajax requires a single object parameter."|gettext}{literal}');
	                return false;
	            } else {
	                if (typeof(obj.json)!=="undefined"){
	                    this.json = obj.json;
	                } else {
	                    this.json = false;
	                }
	                var sUri = gatherURLInfo(obj);
	                //Y.log(sUri);
	                YAHOO.util.Connect.asyncRequest("POST", sUri, {
	                success: function (o) {
	                    //if we're just sending a request and not needing to do
	                    //anything on the completion, we can skip firing the custom event
	                    if (typeof(this.oEvent)!=="undefined") {

	                        //otherwise, we check if we've got JSON coming back to parse
	                        if (this.json!==false) {
	                            //if so, parse it
	                            var oParse = YAHOO.lang.JSON.parse(o.responseText);
	                        } else {
	                            //if not, it's probably HTML we're going to update a view with
	                            var oParse = o.responseText;
	                        }
	                        //fire off the custom event to do some more stuff with
	                        this.oEvent.fire(oParse);
	                    }
	                },
	                    scope: this
	                },obj.data);
	            }
	        }
	    }
	}

}