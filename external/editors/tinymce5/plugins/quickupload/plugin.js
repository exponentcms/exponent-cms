/*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

/*global tinymce:true */

tinymce.PluginManager.add('quickupload', function(editor, url) {
    'use strict';

    // plupload base js folder
   	// use for include swf or xap
   	var basepath = editor.settings.plupload_basepath;

   	// upload post url
   	var purl = editor.settings.upload_url;

   	// callback of uploaded
   	// one arg is plupload xhr response, return false or a image path.
   	var callback = editor.settings.upload_callback;

   	// callback of error
   	var error_call = editor.settings.upload_error;

   	// post params
   	var postdata = editor.settings.upload_post_params;

   	// filesize
   	var filesize = editor.settings.upload_file_size;

    var id = editor.id + '_quickupload';

    editor.ui.registry.addIcon('quickupload', '<?xml version="1.0" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN""http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd"><svg id="' + id + '" version="1.0" xmlns="http://www.w3.org/2000/svg" width="14.000000pt" height="14.000000pt" viewBox="0 0 14.000000 14.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,14.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M0 70 l0 -70 70 0 70 0 0 70 0 70 -70 0 -70 0 0 -70z m45 10 c12 -5 26 -2 36 6 14 13 18 12 28 0 15 -17 7 -36 -15 -36 -8 0 -15 -10 -15 -22 -1 -18 -3 -16 -8 7 -7 35 -46 48 -54 18 -3 -10 -5 5 -5 32 0 34 2 42 7 26 3 -13 15 -27 26 -31z m35 30 c0 -15 -7 -20 -25 -20 -18 0 -25 5 -25 20 0 15 7 20 25 20 18 0 25 -5 25 -20z"/></g></svg>');
    editor.ui.registry.addButton('quickupload', {
        // icon: 'quickupload',
        icon: 'upload',
//        image       : url + '/img/quick_button.svg',
   		id: id,
   		tooltip: 'Quick upload image',
   //		stateSelector: 'img:not([data-mce-object],[data-mce-placeholder])'
        onAction: function () {
//            uploader.start();  // note MUST include this for TinyMCE 5 plugins
        },
        onSetup: function (buttonApi) {

            // add Button id
            document.getElementById(id).closest('button').setAttribute('id', id);

            editor.on('init', function(){
          		var btn = document.getElementById(id);
                  // if (btn) {
                      var ico = btn.childNodes[0].childNodes[0];
//                      var tooltip = editor.theme.panel.controlIdLookup[id];

                       var uploader = new plupload.Uploader({
                           runtimes: 'html5,flash,silverlight,html4' ,
                           browse_button: id ,
                           url: purl ,
                           filters: {
                               max_file_size: filesize ? filesize : "1mb" ,
                               mime_types: [{title: "Image files" , extensions: "jpg,jpeg,gif,png"}] ,
                           } ,
                           multipart_params: postdata ,
                           multi_selection: false ,
                           flash_swf_url: basepath + 'Moxie.swf' ,
                           silverlight_xap_url: basepath + 'Moxie.xap' ,
                           init: {
                               Init: function (up) {
                                   return true;
                               } ,

                               FilesAdded: function (up , files) {
                                  ico.className = 'mce-ico mce-i-restoredraft';
//                                  tooltip.settings.tooltip = "Uploading..";
                                   up.start();
                               } ,

                               UploadProgress: function (up , file) {
//                                  tooltip.settings.tooltip = file.percent + "%";
                               } ,

                               FileUploaded: function (up , file , res) {
                                  ico.className = 'mce-ico mce-i-image';
//                                  tooltip.settings.tooltip = "Insert/edit image";
                                   var file = callback(res , file , up);
                                   if (!file) return;
                                   editor.focus();
                                   editor.selection.setContent(editor.dom.createHTML('img' , {src: file}));
                               } ,

                               UploadComplete: function (up , files) {

                               } ,

                               Error: function (up , err) {
                                   error_call(err , up);
                               }
                           }
                       });

                       uploader.init();
//                   }
           	});
        }
   	});

});
