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
				////console.debug(o.data[i]);
				images += '<img src="'+EXPONENT.PATH_RELATIVE+'thumb.php?constraint=1&id='+o.data[i].id+'&width=100&height=200">';
			}
			//console.debug(images);
			this.setBody(images);
		},this);
			
		getfiles.fetch({module:"filemanagermodule",action:"grab_files",json:1});
	
	},this, true);
};
YAHOO.lang.extend(EXPONENT.filepicker, YAHOO.widget.Panel);
