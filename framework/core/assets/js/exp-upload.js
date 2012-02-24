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

//useful shortcuts
var YUD = YAHOO.util.Dom, YUE = YAHOO.util.Event;

//initialize with the SWF url
YAHOO.widget.Uploader.SWFURL = URL_FULL+"yui/build/uploader/assets/Uploader.swf";

//set up our namespace
YAHOO.namespace("upload");

//object creation ahoy
YAHOO.upload = {
	pics : null,
	files : null,
	uploader : null,
	
	init : function() {
		this.files = {};
		
		this.uploader = new YAHOO.widget.Uploader('uploader');
		
		//certain things can only happen once the SWF is ready to rock
		this.uploader.addListener('contentReady',	this.swfReady);
		
		//event handlers
		this.uploader.addListener('fileSelect',			this.onFileSelect);
		this.uploader.addListener('uploadStart',		this.onUploadStart);
		this.uploader.addListener('uploadError',		this.onUploadError);
		this.uploader.addListener('uploadProgress',		this.onUploadProgress);
		this.uploader.addListener('uploadCancel',		this.onUploadCancel);
		this.uploader.addListener('uploadComplete',		this.onUploadComplete);
		this.uploader.addListener('uploadCompleteData', this.onUploadCompleteData);

		//button handlers (browse is attached in swfReady, so you can't click it until the SWF has loaded)
		YUE.on('upload', 'click',  this.upload, null, this);
		YUE.on('cancel', 'click',  this.cancel, null, this);
		YUE.on('export', 'click',  this.showExport, null, this);
		
		//handle clicking on export text field
		YUE.on('export_text', 'click', function(e) { YUE.getTarget(e).select(); });
		
		//One click handler for the entire list
		YUE.on('files', 'click', this.listClick, null, this);
	},
	
	//Buttons
	browse : function(e) {
		this.uploader.clearFileList();
		
		this.uploader.browse(true, [{description : "Images", extensions : "*.jpg; *.gif; *.png"}]);
	},
	
	upload : function(e) {
		YUD.get('browse').style.display = 'none';
		
		this.uploader.uploadAll('http://tivac.com/upload/upload.php', 'GET');
	},
	
	cancel : function(e) {
		this.uploader.cancel();
	},
	
	//hide "Export" button, show the text area
	showExport : function(e) {
		YUD.get('export').style.display = 'none';
		YUD.get('export_text').style.display = 'block';
	},
	
	//Click Handlers
	listClick : function(e) {
		var tgt = YUE.getTarget(e);
		
		//if it's an input element, focus all the text for easy copying
		if(tgt.nodeName.toLowerCase() == 'input') {
			tgt.select();
		} else {
			//otherwise the only thing we care about is deleting
			var li = YUD.getAncestorByTagName(tgt, 'li');
			
			if(!li) {
				return;
			}
			
			this.deleteFile(li);
		}
	},
	
	//Events
	
	//general function that wires up anything that depends on the SWF being ready to rock
	swfReady : function(e) {
		YUE.on('browse', 'click',  YAHOO.upload.browse, null, YAHOO.upload);
	},
	
	onFileSelect : function(e) {
		//store the file list for safekeeping
		YAHOO.upload.pics = e.fileList;
		
		var ul = YUD.get('files');
		YUD.removeClass(YUD.getLastChild(ul), 'last');
		
		//iterate through files and create <li>s for each
		for(var f in e.fileList) if (e.fileList.hasOwnProperty(f)) {
			var file = e.fileList[f];
			
			var li = YUD.get('file_skeleton').cloneNode(true);
				li.id = file.id;
			
			YUD.getFirstChild(li).innerHTML = file.name;
			
			ul.appendChild(li);
		}
		
		//reattach that last class so the borders are ok
		YUD.addClass(YUD.getLastChild(ul), 'last');
		
		//show the upload button, because now there's files to upload
		YUD.get('upload').style.display = 'block';
	},
	
	onUploadStart : function(e) {
		YUD.get('upload').style.display = 'none';
		YUD.get('cancel').style.display = 'block';
		
		//clear out export field just in case
		YUD.get('text_export').value = '';
	},
	
	//TODO: Handle error, reset?
	onUploadError : function(e) {
		
	},
	
	//TODO: Clean up the page state
	onUploadCancel : function(e) {
		YUD.get('cancel').style.display = 'none';
		YUD.get('upload').style.display = 'block';
	},
	
	onUploadProgress : function(e) {
		var div = YUD.getFirstChild(e.id);
		
		//100s are used to convert float -> int via the floor, then convert it back
		var percent = Math.floor(100 - ((e.bytesLoaded / e.bytesTotal) * 100)) / 100;
		
		//688 is the rendered with of each div we shuffle this across, it's the same as the CSS value
		//that positions the background image
		div.style.backgroundPosition = (-1 * (688 * percent)) + "px 0";
	},
	
	onUploadComplete : function(e) {
		delete(YAHOO.upload.pics[e.id]);
		
		//keep track of how many files are left
		var empty = true;
		for(var i in YAHOO.upload.pics) if (YAHOO.upload.pics.hasOwnProperty(i)) {
			empty = false;
		}
		
		//if none are left, call the final function to clean up a bit
		if(empty) {
			YAHOO.upload.allUploadsComplete.apply(YAHOO.upload);
		}
	},
	
	//Got data back from server after an upload
	onUploadCompleteData : function(e) {
		var out;
		var oldli = YUD.get(e.id);
			oldli.id = "removing";
		
		//parse the JSON
		try {
			out = YAHOO.lang.JSON.parse(e.data);
		} catch (err) {
			//we can't do anything with invalid JSON
			return;
		}
		
		//get a copy of the upload HTML to munge up
		var li = YUD.get('upload_skeleton').cloneNode(true);
			li.id = e.id;
		
		if(YUD.hasClass(oldli, 'last')) {
			YUD.addClass(li, 'last');
		}
		
		//give the thumbnail link life
		var a = YUD.getFirstChild(li);
			a.href = out.url;
			a.style.width = (out.width + 20) + "px";
		
		var img = YUD.getFirstChild(a);
			img.src = out.thb;
			img.alt = out.name;
		
		//update all those form fields (YIKES)
		var inputs = YUD.getLastChild(li).getElementsByTagName('p');
		for(var i = inputs.length - 1; i >= 0; i--) {
			var p = inputs[i];
			
			var label = p.getElementsByTagName('label')[0];
				label.setAttribute('for', e.id + label.getAttribute('for'));
			
			var input = p.getElementsByTagName('input')[0];
				input.setAttribute('id',  e.id + input.getAttribute('id'));
				input.setAttribute('value', ((p.className) ? out.links[p.className] : out.url));
		}
		
		//update the export_text field
		YUD.get('export_text').value += out.url + "\n";
		
		//replace the old li with the new one
		YUD.get('files').replaceChild(li, oldli);
	},
	
	//General Functions
	allUploadsComplete : function() {
		YUD.get('cancel').style.display = 'none';
		YUD.get('export').style.display = 'block';
	},
	
	deleteFile : function(li) {
		//only want to remove files in selection phase, not after they've been uploaded
		if(!YUD.hasClass('uploaded')) {
			
			//remove from uploader
			this.uploader.removeFile(li.id);
			
			//remove from local cache of files in uploader queue
			delete(this.pics[li.id]);
			
			//remove DOM node
			var ul = YUD.get('files');
				ul.removeChild(li);
			
			//if we don't have at least one valid element left, hide the upload button
			if(ul.getElementsByTagName('li').length < 3) {
				YUD.get('upload').style.display = 'none';
			}
		}
	}
}

YUE.onAvailable('bd', YAHOO.upload.init, null, YAHOO.upload);