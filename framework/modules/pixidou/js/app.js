/**
*	Pixidou - Open Source AJAX Image Editor
*	Get the whole thing going here
*/
app = {	
	init: function(){
		// all event handling here				
		YAHOO.util.Event.on('uploadButton', 'click', uploader.carry);
		YAHOO.util.Event.on('cropTool', 'click', pixidou.initCropTool);
		YAHOO.util.Event.on('applyCrop', 'click', pixidou.applyCropTool);
		YAHOO.util.Event.on('disableCropTool', 'click', pixidou.disableCropTool);
		YAHOO.util.Event.on('rotateTool', 'click', function(){
			pixidou.applyRotateTool('ffwd', pixidou.rotateDegree);
		});
		YAHOO.util.Event.on('resizeTool', 'click', pixidou.initResizeTool);
		YAHOO.util.Event.on('disableResizeTool', 'click', pixidou.disableResizeTool);
		YAHOO.util.Event.on('applyResize', 'click', pixidou.applyResize);
		YAHOO.util.Event.on('flipToolH', 'click', function(){
			pixidou.applyFlipTool('horizontal');
		});
		YAHOO.util.Event.on('flipToolV', 'click', function(){
			pixidou.applyFlipTool('vertical');
		});
		YAHOO.util.Event.on('saveJpgTool', 'click', function(){
			pixidou.saveImage('jpg');
		});
		YAHOO.util.Event.on('savePngTool', 'click', function(){
			pixidou.saveImage('png');
		});
		YAHOO.util.Event.on('saveGifTool', 'click', function(){
			pixidou.saveImage('gif');
		});
		
		YAHOO.util.Event.on('quit', 'click', pixidou.returnToPicker,"doNotSave");
		YAHOO.util.Event.on('saveAsIs', 'click', pixidou.returnToPicker,"saveAsIs");
		YAHOO.util.Event.on('saveAsCopy', 'click', pixidou.returnToPicker,"saveAsCopy");
		
		YAHOO.util.Event.on('openFile', 'click', ui.showUploadForm);
		YAHOO.util.Event.on('negativeTool', 'click', pixidou.applyNegativeTool);
		YAHOO.util.Event.on('undoTool', 'click', pixidou.applyUndoTool);
		YAHOO.util.Event.on('tintTool', 'click', ui.showTintPanel);
		YAHOO.util.Event.on('contrastTool', 'click', ui.showContrastPanel);
		YAHOO.util.Event.on('brightnessTool', 'click', ui.showBrightnessPanel);
		YAHOO.util.Event.on('aboutTool', 'click', ui.showAboutPanel);
	},
	
	/**
	*	Shows a loading panel while loading the page at first
	*/
	showInitPanel: function(){
		app.loadingPanel = new YAHOO.widget.Panel("loadingApp",  
                                                    { width: "19em", 
                                                      fixedcenter: true, 
                                                      close: false, 
                                                      draggable: false, 
                                                      zindex:4,
                                                      modal: true,
                                                      visible: false
                                                    } 
                                                );
		app.loadingPanel.setHeader("Hold on while we get everything ready");
		app.loadingPanel.setBody("<img src=\"http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
		app.loadingPanel.render(document.body);
		app.loadingPanel.show();
	}
};

YAHOO.util.Event.addListener(window, 'load', function(){
	// load the rest
	ui.init();
	layout.init();
	keys.init();
	ui.disableApplyButton();
	YAHOO.util.Dom.removeClass(YAHOO.util.Dom.get('doc3'), 'hide');
	app.loadingPanel.hide();
});

app.init();
app.showInitPanel();