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

//FIXME convert to yui3
EXPONENT.filepicker = function(id) {

	// var init = function(){
	// 	if (!loader.inserted.json || !loader.inserted.json){
	// 		loader.require('container');
	// 		loader.dirty = true;
	// 		loader.insert({},'js');
	// 	} 
	// }();

	EXPONENT.filepicker.superclass.constructor.call(this,
		id || YAHOO.util.Dom.generateId() , 
		{
			width: "600px", 
			height: "600px", 
			fixedcenter: true, 
			constraintoviewport: true, 
			underlay: "shadow", 
			close: false, 
			modal: true, 
			close: true, 
			visible: false, 
			draggable: true
		}
	);
	
	this.setHeader("File Picker");
	this.setBody('Loading Files');
	YAHOO.util.Dom.setStyle(this.body, 'textAlign', 'center');
	YAHOO.util.Dom.setStyle(this.body, 'height', '550px');
	YAHOO.util.Dom.setStyle(this.body, 'overflow', 'scroll');
	this.render(document.body);
	
	
	var pickers = YAHOO.util.Dom.getElementsByClassName('filepickerlink', 'a');
	YAHOO.util.Event.on(pickers, 'click', function(e){
		YAHOO.util.Event.stopEvent(e);
		var clickedEl = YAHOO.util.Event.getTarget(e);
		//this.setBody(clickedEl.id);
		this.show();
		var getfiles = new EXPONENT.AjaxEvent();
		getfiles.subscribe(function (o) {
			var images = "";
			for (var i=0;i<o.data.length;i++){
				////Y.log(o.data[i]);
				images += '<img src="'+EXPONENT.PATH_RELATIVE+'thumb.php?constraint=1&id='+o.data[i].id+'&width=100&height=200">';
			}
			//Y.log(images);
			this.setBody(images);
		},this);
			
		getfiles.fetch({module:"filemanagermodule",action:"grab_files",json:1});
	
	},this, true);

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

};
YAHOO.lang.extend(EXPONENT.filepicker, YAHOO.widget.Panel);
