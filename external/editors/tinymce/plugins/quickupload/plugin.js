/*global tinymce:true */

tinymce.PluginManager.add('quickupload', function(editor, url) {

    editor.addButton('quickupload', {
   		//icon: 'image',
        icon: 'quickupload',
        image       : url + '/img/quick_button.gif',
   		id: editor.id + '_quickupload',
   		tooltip: 'Quick upload image',
   //		stateSelector: 'img:not([data-mce-object],[data-mce-placeholder])'
   	});

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

	editor.on('init', function(){
		var id = editor.id + '_quickupload';
		var btn = document.getElementById(id);
        if (btn) {
            var ico = btn.childNodes[0].childNodes[0];
            var tooltip = editor.theme.panel.controlIdLookup[id];

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
                        tooltip.settings.tooltip = "Uploading..";
                        up.start();
                    } ,

                    UploadProgress: function (up , file) {
                        tooltip.settings.tooltip = file.percent + "%";
                    } ,

                    FileUploaded: function (up , file , res) {
                        ico.className = 'mce-ico mce-i-image';
                        tooltip.settings.tooltip = "Insert/edit image";
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
        }
	});
});
