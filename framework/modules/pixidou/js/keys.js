/**
*	Pixidou - Open Source AJAX Image Editor
*	All keyboard handling here
*/
keys = {
	init: function(){
		// SHIFT + O for upload panel
		var uploadKey = new YAHOO.util.KeyListener(document, { shift:true, keys:79 }, {fn: ui.showUploadForm});
		uploadKey.enable();
		
		// SHIFT + J for save jpg
		var saveJpegKey = new YAHOO.util.KeyListener(document, { shift:true, keys:74 }, function(){
			pixidou.saveImage('jpg');
		});
		saveJpegKey.enable();
				
		// SHIFT + P for save png
		var savePngKey = new YAHOO.util.KeyListener(document, { shift:true, keys:80 }, function(){
			pixidou.saveImage('png');
		});
		savePngKey.enable();
		
		// SHIFT + G for save gif
		var saveGifKey = new YAHOO.util.KeyListener(document, { shift:true, keys:71 }, function(){
			pixidou.saveImage('gif');
		});
		saveGifKey.enable();
		
		// SHIFT + Z for undo
		var undoKey = new YAHOO.util.KeyListener(document, { shift:true, keys:90 }, {fn: pixidou.applyUndoTool});
		undoKey.enable();
		
		// SHIFT + B for brightness
		var brightnessKey = new YAHOO.util.KeyListener(document, { shift:true, keys:66 }, {fn: ui.showBrightnessPanel});
		brightnessKey.enable();
		
		// SHIFT + C for contrast
		var contrastKey = new YAHOO.util.KeyListener(document, { shift:true, keys:67 }, {fn: ui.showContrastPanel});
		contrastKey.enable();
		
		// SHIFT + V for crop
		var cropKey = new YAHOO.util.KeyListener(document, { shift:true, keys:86 }, {fn: pixidou.initCropTool});
		cropKey.enable();
		
		// SHIFT + H for horizontal flip
		var flipHKey = new YAHOO.util.KeyListener(document, { shift:true, keys:72 }, function(){
			pixidou.applyFlipTool('horizontal');
		});
		flipHKey.enable();
		
		// SHIFT + Y for horizontal flip
		var flipVKey = new YAHOO.util.KeyListener(document, { shift:true, keys:89 }, function(){
			pixidou.applyFlipTool('vertical');
		});
		flipVKey.enable();
		
		// SHIFT + N for crop
		var negativeKey = new YAHOO.util.KeyListener(document, { shift:true, keys:78 }, {fn: pixidou.applyNegativeTool});
		negativeKey.enable();
		
		// SHIFT + X for resize
		var resizeKey = new YAHOO.util.KeyListener(document, { shift:true, keys:88 }, {fn: pixidou.initResizeTool});
		resizeKey.enable();
		
		// SHIFT + R for rotate
		var rotateKey = new YAHOO.util.KeyListener(document, { shift:true, keys:82 }, function(){
			pixidou.applyRotateTool('ffwd', pixidou.rotateDegree);
		});
		rotateKey.enable();
		
		// SHIFT + T for tint
		var tintKey = new YAHOO.util.KeyListener(document, { shift:true, keys:84 }, {fn: ui.showTintPanel});
		tintKey.enable();
		
		// SHIFT + DOWN for zoom in
		var zoomInKey = new YAHOO.util.KeyListener(document, { shift:true, keys:38 }, {fn: pixidou.zoomIn});
		zoomInKey.enable();
		
		// SHIFT + UP for zoom out
		var zoomOutKey = new YAHOO.util.KeyListener(document, { shift:true, keys:40 }, {fn: pixidou.zoomOut});
		zoomOutKey.enable();		
		
		// SHIFT + A for about
		var aboutPanelKey = new YAHOO.util.KeyListener(document, { shift:true, keys:65 }, {fn: ui.showAboutPanel});
		aboutPanelKey.enable();
	}
};