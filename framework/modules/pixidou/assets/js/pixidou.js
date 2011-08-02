/**
*	Pixidou - Open Source AJAX Image Editor
*	This is our main class, doing all the image manipulations here
*/
pixidou = {
	image: null, // the current image we're dealing
	imageWidth: null, // current image width
	imageHeight: null, // current image height
	crop: null, // crop tool
	resize: null, // resize tool
	rotateDegree: 90, // default rotation degree
	zoomLevel: 100, // default zoom level (can be implemented later on)
	imageHistory: Array(), // contains all our images, used for undoing
	imageHistoryIndex: 0, // index to know where we are in our image history
	savedState: true, // whether the image must be saved or not in order to continue (useful for resize/crop)
	currentTool: null, // current tool, currently resize/crop
	
	/**
	*	Initializes the whole thing to get going
	*	@params image String
	*	@params width Int
	*	@params height Int
	*/
	init: function(image, width, height){
		// set our image and dimensions
		pixidou.image = image;
		pixidou.imageWidth = width;
		pixidou.imageHeight = height;
	},
	
	/**
	*	Initializes our crop tool
	*/
	initCropTool: function(){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// check whether we must save current image before continuing, especially prior to a resize
			pixidou.checkSavedState();
			
			// zoom to 100%
			pixidou.zoomTo(100);
			
			// init the crop tool with the yuiImg id
			pixidou.crop = new YAHOO.widget.ImageCropper('yuiImg');
			
			// remove any listeners for the apply/cancel button
			YAHOO.util.Event.removeListener('applyTool', 'click');
			YAHOO.util.Event.removeListener('cancelTool', 'click');
			
			// set new listeners for our buttons in the footer
			YAHOO.util.Event.addListener('applyTool', 'click', pixidou.applyCropTool);
			YAHOO.util.Event.addListener('cancelTool', 'click', pixidou.disableCropTool);
			
			// show apply/cancel buttons
			ui.enableApplyButton();
			
			// we havent saved the file yet
			pixidou.savedState = false;
			
			// set the current tool to crop
			pixidou.currentTool = 'crop';
		}
	},
	
	/**
	*	Disables our crop tool
	*/
	disableCropTool: function(){
		pixidou.crop.destroy();
		// set current tool to null
		pixidou.currentTool = null;
	},
	
	/**
	*	Applies our crop tool
	*/
	applyCropTool: function(){
		// show loading panel
		ui.showLoadingPanel();
		
		// get the coordinates
		var coordinates = pixidou.crop.getCropCoords();
		
		// AJAX request
		//var request = YAHOO.util.Connect.asyncRequest('GET', 'crop.php?image=' + pixidou.image + '&cropStartX=' + coordinates.left +'&cropStartY=' + coordinates.top +'&width=' + coordinates.width +'&height=' + coordinates.height + '&zoomLevel=' + pixidou.zoomLevel, {
		var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=crop&image=' + pixidou.image + '&cropStartX=' + coordinates.left +'&cropStartY=' + coordinates.top +'&width=' + coordinates.width +'&height=' + coordinates.height + '&zoomLevel=' + pixidou.zoomLevel, {
			success: function(o){
				// update data from response
				pixidou.updateThruJson(o);
				
				// remove any listeners for the apply button
				ui.disableApplyButton();
			},
			
			failure: function(o){
				// any errors
				pixidou.error(o);
			}
		});
	},
	
	/**
	*	Allows rotation of images. No save needed
	*	@params direction String (not currently used)
	*	@params degrees Int (90,180,270)
	*/
	applyRotateTool: function(direction, degrees){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// checks whether we need to save beforehand
			pixidou.checkSavedState();
			
			// please wait
			ui.showLoadingPanel();
			
			// AJAX request
			var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=rotate&image=' + pixidou.image + '&direction=' + direction + '&degrees=' + degrees, {
				success: function(o){
					// update data
					pixidou.updateThruJson(o);
				},
				
				failure: function(o){
					// any errors
					pixidou.error(o);
				}
			});
		}
	},
	
	/**
	*	Flips an image
	*	@params direction String H/V
	*/
	applyFlipTool: function(direction){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// whether to be saved or not
			pixidou.checkSavedState();
			
			// show our loading panel
			ui.showLoadingPanel();
			
			// AJAX request
			var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=flip&direction=' + direction + '&image=' + pixidou.image + '&direction=' + direction, {
				success: function(o){
					pixidou.updateThruJson(o);
				},
				
				failure: function(o){
					// any errors
					pixidou.error(o);
				}
			});
		}
	},
	
	/**
	*	Init our resize tool
	*/
	initResizeTool: function(){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// once again check if need to be saved
			pixidou.checkSavedState();
			
			// zoom to 100%
			pixidou.zoomTo(100);
			
			// init our control
			pixidou.resize = new YAHOO.util.Resize('yuiImg', {
	        	handles: 'all',
	            knobHandles: true,
	            height: pixidou.imageHeight + 'px',
	            width: pixidou.imageWidth + 'px',
	            proxy: true,
	            ghost: true,
	            status: true,
	            draggable: false
	        });
			
			// remove any listeners for the apply/cancel button
			YAHOO.util.Event.removeListener('applyTool', 'click');
			YAHOO.util.Event.removeListener('cancelTool', 'click');
			
			// set new listeners
			YAHOO.util.Event.addListener('applyTool', 'click', pixidou.applyResizeTool);
			YAHOO.util.Event.addListener('cancelTool', 'click', pixidou.disableResizeTool);
			
			// show the buttons in the footer
			ui.enableApplyButton();
			
			// tell we must save if new op
			pixidou.savedState = false;
			
			// sets the current tool
			pixidou.currentTool = 'resize';
		}
	},
	
	/**
	*	Disables the resize tool
	*/
	disableResizeTool: function(){
		pixidou.resize.reset();
		pixidou.resize.destroy();
	
		pixidou.savedState = true;
		pixidou.currentTool = null;
		
	},
	
	/**
	*	Resizes the current image
	*/
	applyResizeTool: function(){
		// show the loading panel
		ui.showLoadingPanel();
		
		// AJAX request
		var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=resize&image=' + pixidou.image + '&width=' + parseInt(YAHOO.util.Dom.getStyle('yuiImg', 'width'), 10) + '&height=' + parseInt(YAHOO.util.Dom.getStyle('yuiImg', 'height'), 10) + '&zoomLevel=' + pixidou.zoomLevel , {
			success: function(o){
				pixidou.updateThruJson(o);
				
				// remove any listeners for the apply/cancel button
				ui.disableApplyButton();
			},
			
			failure: function(o){
				// any errors
				pixidou.error(o);
			}
		});
	},
	
	/**
	*	Negative for an image
	*/
	applyNegativeTool: function(){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// save check
			pixidou.checkSavedState();
			
			// loading panel
			ui.showLoadingPanel();
			
			// AJAX request
			var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=negative&image=' + pixidou.image , {
				success: function(o){
					pixidou.updateThruJson(o);
				},
				
				failure: function(o){
					// any errors
					pixidou.error(o);
				}
			});
		}
	},
	
	/**
	*	Tints an image
	*/
	applyTintTool: function(color){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// save check
			pixidou.checkSavedState();
			
			// show the loading panel
			ui.showLoadingPanel();
			
			// AJAX request
			var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=tint&color=' + color + '&image=' + pixidou.image , {
				success: function(o){
					pixidou.updateThruJson(o);
				},
				
				failure: function(o){
					// any errors
					pixidou.error(o);
				}
			});
		}
	},
	
	/**
	*	Applies contrast to an image
	*	@params value Int (-127 to 127)
	*/
	applyContrastTool: function(value){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			
			// save check
			pixidou.checkSavedState();
			
			// show loading panel
			ui.showLoadingPanel();
			
			// AJAX request
			var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=contrast&value=' + value + '&image=' + pixidou.image , {
				success: function(o){
					pixidou.updateThruJson(o);
				},
				
				failure: function(o){
					// any errors
					pixidou.error(o);
				}
			});
		}
	},
	
	/**
	*	Applies brightness to an image
	*	@params value Int (-127 to 127)
	*/
	applyBrightnessTool: function(value){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// save check
			pixidou.checkSavedState();
			
			// show loading panel
			ui.showLoadingPanel();
			
			// AJAX request
			var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=brightness&value=' + value + '&image=' + pixidou.image , {
				success: function(o){
					pixidou.updateThruJson(o);
				},
				
				failure: function(o){
					// any errors
					pixidou.error(o);
				}
			});
		}
	},
	
	/**
	*	Zoom in function
	*/
	zoomIn: function(){
		if(pixidou.zoomLevel < 100){
			// increments by 25% if we are less than 100%
			pixidou.zoomLevel = pixidou.zoomLevel + 25;
		}
		
		// update our image
		pixidou.updateZoom();
	},
	
	/**
	*	Zoom out function
	*/
	zoomOut: function(){
		if(pixidou.zoomLevel > 25){
			// same idea as above, in packets of 25%
			pixidou.zoomLevel = pixidou.zoomLevel - 25;
		}
		
		// update our image
		pixidou.updateZoom();
	},
	
	/**
	*	Zooms to a certain level
	*	@params Int zoomLeve 25/50/75/100
	*/
	zoomTo: function(zoomLevel){
		pixidou.zoomLevel = zoomLevel;
		pixidou.updateZoom();
	},
	
	/**
	*
	*/
	updateZoom: function(){
		pixidou.checkSavedState();
		var updatedWidth = (pixidou.imageWidth * pixidou.zoomLevel) / 100;
		var updatedHeight = (pixidou.imageHeight * pixidou.zoomLevel) / 100;
		//var zoomAnim = new YAHOO.util.Anim('yuiImg', {width: {to: updatedWidth }, height: {to: updatedHeight}}, 1, YAHOO.util.Easing.easeOut);
		//zoomAnim.animate();
		
		YAHOO.util.Dom.setStyle('yuiImg', 'height', updatedHeight+'px');
		YAHOO.util.Dom.setStyle('yuiImg', 'width', updatedWidth+'px');
		YAHOO.util.Dom.get('zoomValue').innerHTML = pixidou.zoomLevel;
		//YAHOO.util.Dom.get('yuiImg').width = updatedWidth;
		//YAHOO.util.Dom.get('yuiImg').height = updatedHeight;
	},
	
	/**
	*	Checks whether an image needs to be saved before continuing, prompting the user 
	*/
	checkSavedState: function(){
		if(!pixidou.savedState){
			ui.showSavePanel();
		}
	},
	
	/**
	*	Checks whether an image has been uploaded or not
	*/
	checkImage: function(){
		if(pixidou.image == null){
			alert('No image has been uploaded');
			return false;
		}
		else{
			return true;
		}
	},
	
	/**
	*	All errors here
	*/
	error: function(o){
		ui.hideLoadingPanel();
		alert(o.responseText);
	},
	
	/**
	*	Undo handling here
	*/
	applyUndoTool: function(){
		// get current image index and check if there's more than one
		if(pixidou.imageHistoryIndex >= 2){
			ui.showLoadingPanel();
			
			// remove object from array
			pixidou.imageHistory = pixidou.imageHistory.splice(0, pixidou.imageHistory.length - 1);
			
			// decrease image index
			pixidou.imageHistoryIndex = pixidou.imageHistoryIndex - 2;
			
			// get the object at that positions
			var imageObject = pixidou.imageHistory[pixidou.imageHistoryIndex];
			
			// update our image container
			pixidou.updateImage(imageObject.image, imageObject.width, imageObject.height);
		}
	},
	
	/**
	*	Update the image with the JSON data received from the PHP scripts
	*	@params String responseData 
	*/
	updateThruJson: function(responseData){
		// parse json data
		var jsonData = YAHOO.lang.JSON.parse(responseData.responseText);
		
		// check if we have any errors
		
		if(jsonData.error == undefined){
			// set new image for our object
			pixidou.image = jsonData.image;
			pixidou.imageWidth = jsonData.width;
			pixidou.imageHeight = jsonData.height;
			
			// update our image container
			pixidou.updateImage(jsonData.image, jsonData.width, jsonData.height);
		}
		else{
			alert(jsonData.error);
		}
	},
	
	/**
	*	Updates the image to the image container
	*	@params String image
	*	@params Int width
	*	@params Int height
	*/
	updateImage: function(image, width, height){
		/**
		var viewportWidth = YAHOO.util.Dom.getViewportWidth();
		
		if(width > viewportWidth){
			if((width * 0.75) > viewportWidth){
				if((width * 0.5) > viewportWidth){
					pixidou.zoomLevel = 25;	
				}
				else{
					pixidou.zoomLevel = 50;
				}
			}
			else{
				pixidou.zoomLevel = 75;
			}
			
			// update the width and height
			width = (width * pixidou.zoomLevel) / 100;
			height = (height * pixidou.zoomLevel)/100;
		}*/
		pixidou.image = image;
		pixidou.imageWidth = width;
		pixidou.imageHeight = height;
		
		YAHOO.util.Dom.get('imageContainer').innerHTML = '<img id="yuiImg" src="'+EXPONENT.URL_FULL+'framework/modules/pixidou/images/' + image + '" width="' + width + '" height="' + height + '" alt="" style="width:' + width + 'px;height:' + height + 'px;" />';

		// add it to our history
		var imageObject = new Object;
		imageObject.image = image;
		imageObject.width = width;
		imageObject.height = height;
		
		pixidou.imageHistory[pixidou.imageHistoryIndex] = imageObject;
		pixidou.imageHistoryIndex ++;
		
		// hide our loading panel if any
		ui.hideLoadingPanel();
		
		// set state to ok
		pixidou.savedState = true;
	},
	
	/**
	*	Saves/converts the image
	*	@params String type gif|png|jpg
	*/
	saveImage: function(type){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// save check
			pixidou.checkSavedState();
			
			// show loading panel
			ui.showLoadingPanel();
			
			// AJAX request
			var request = YAHOO.util.Connect.asyncRequest('GET', EXPONENT.URL_FULL+'framework/modules/pixidou/image.php?action=save&type=' + type + '&image=' + pixidou.image , {
				success: function(o){
					// parse json data
					var jsonData = YAHOO.lang.JSON.parse(o.responseText);
					
					location.href = 'download.php?file=' + jsonData.image;

					ui.hideLoadingPanel();
				},
				
				failure: function(o){
					// any errors
					pixidou.error(o);
				}
			});
		}
	},
	/**
	*	Returns back to File Picker
	*	@param Object event event
	*	@param String event event
	*/
	returnToPicker: function(event,exitType){
		// check if image has been uploaded before
		if(pixidou.checkImage()){
			// save check
			pixidou.checkSavedState();
			
			// show loading panel
			ui.showLoadingPanel();
			
			//checking to see if we're coming from FCK editor
			var fromFCK = YAHOO.util.Dom.get('fromFCK').value ? "&fck=1" : "";
			
			// Grab some vars and jump back to the editor
            var surl = EXPONENT.URL_FULL+"index.php?controller=pixidou&action=exitEditor&exitType="+exitType+"&ajax_action=1"+fromFCK+"&update="+YAHOO.util.Dom.get('update').value+"&fid="+YAHOO.util.Dom.get('fid').value+"&cpi="+pixidou.image;
            // console.debug(surl);
            // console.debug(fromFCK);
            window.location = surl;
		}
	}
};
