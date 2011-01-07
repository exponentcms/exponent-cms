/**
*	Pixidou - Open Source AJAX Image Editor
*	AJAX upload, based from http://thecodecentral.com/2007/09/04/asynchronous-file-upload-yuis-approach
*/
uploader = {
	carry: function(){
		// show loading panel
		ui.showLoadingPanel();
		
		// set form
		YAHOO.util.Connect.setForm('uploadForm', true);
		// upload image
		YAHOO.util.Connect.asyncRequest('POST', 'upload.php', {
			upload: function(o){
				// update data
				pixidou.updateThruJson(o);
				
				// hide our upload form
				ui.hideUploadForm();
				
				// hide loading panel
				ui.hideLoadingPanel();
			}
		});
	},
	fromServer: function(){
	    pixidou.updateImage('4762.jpg',600,388);
				
				// hide our upload form
				ui.hideUploadForm();
	}
};