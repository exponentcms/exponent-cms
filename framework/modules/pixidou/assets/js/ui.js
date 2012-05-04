/**
*	Pixidou - Open Source AJAX Image Editor
*	All UI here, such as panels, dialogs, color pickers, etc
*/
ui = {
	init: function(){
		// custom alert box
		// our dialog for info, to show messages to the users
		ui.dialogInfo = new YAHOO.widget.SimpleDialog("alertDialog", 
														{ 	width: "300px",
															fixedcenter: true,
															visible: false,
															draggable: false,
															zIndex: 9999,
															close: true,
															modal: true,
															effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25},
															constraintoviewport: true,
															buttons: [ { text:"close", handler: function(){this.hide();}, isDefault:true }]
														});
		ui.dialogInfo.setHeader("Pixidou info");
		// Render the Dialog
		ui.dialogInfo.render(document.body);
		
		// help about panel
		ui.aboutPanel = new YAHOO.widget.Panel("aboutPanel", 
												{ 	width:"35em", 
													fixedcenter: true, 
                                                    close: true, 
                                                    draggable: true, 
                                                    zindex:4,
                                                    modal: true,
                                                    visible: false 
												} 
											);
		ui.aboutPanel.render(document.body);
		
		// tabview for about panel
		ui.aboutTab = new YAHOO.widget.TabView('aboutTab');
		
		// create our upload form
		ui.uploadForm = new YAHOO.widget.Dialog("uploadPanel", 
				{ width : "30em",
				  fixedcenter : true,
				  visible : false, 
				  modal : true,
				  constraintoviewport : true,
				  buttons : [ 	{ text:"Browse Server", handler: uploader.fromServer, isDefault:true },
				                { text:"Upload", handler: uploader.carry},
					      		{ text:"Cancel", handler: function(){this.cancel();} } ]
					});
		ui.uploadForm.render(document.body);
		
		// create our loading panel, useful when processing images
		ui.loadingPanel = new YAHOO.widget.Panel("loading",  
                                                    { width: "19em", 
                                                      fixedcenter: true, 
                                                      close: false, 
                                                      draggable: false, 
                                                      zindex:4,
                                                      modal: true,
                                                      visible: false
                                                    } 
                                                );
		ui.loadingPanel.setHeader("Hold on while we process your image ...");
		ui.loadingPanel.setBody("<img src=\"http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
		ui.loadingPanel.render(document.body);
		
		// create our dialog for tint
		ui.tintPickerPanel = new YAHOO.widget.Dialog("tintPickerPanel", { 
				width : "30em",
				close: true,
				fixedcenter : true,
				visible : false, 
				modal: true,
				constraintoviewport : true,
				buttons : [ { text:"Apply", handler:function(){
												pixidou.applyTintTool(ui.tintPicker.get("hex"));
												this.cancel();
											}, isDefault:true },
							{ text:"Cancel", handler: function(){
												this.cancel();
											}
							}]
		});
		
		// then add the picker control
        ui.tintPickerPanel.renderEvent.subscribe(function(){
        	if (!ui.tintPicker) {
        		ui.tintPicker = new YAHOO.widget.ColorPicker("tintPicker", {
						container: ui.tintPickerPanel,
						images: {
							PICKER_THUMB: EXPONENT.PATH_RELATIVE+"framework/modules/pixidou/assets/images/picker_thumb.png",
							HUE_THUMB: EXPONENT.PATH_RELATIVE+"framework/modules/pixidou/assets/images/hue_thumb.png"
						}
				});
        	}
        });
        
		ui.tintPickerPanel.render(document.body);
		
		// create dialog for our slider
		ui.contrastPanel = new YAHOO.widget.Dialog("contrastPanel", { 
				width : "22em",
				close: true,
				fixedcenter : true,
				visible : false, 
				modal: true,
				constraintoviewport : true,
				buttons : [ { text:"Apply", handler:function(){
												pixidou.applyContrastTool(ui.contrastSlider.getValue() - 127);
												this.cancel();
											}, isDefault:true },
							{ text:"Cancel", handler: function(){
												this.cancel();
											}
							}]
		});
		
		// then add the slider control
        ui.contrastPanel.renderEvent.subscribe(function(){
        	if (!ui.contrastSlider) {
				ui.contrastSlider = YAHOO.widget.Slider.getHorizSlider('contrastSliderBg', 'contrastSliderThumb', 0, 254, 1);
				ui.contrastSlider.setValue(127);
				ui.contrastSlider.subscribe("change", function(offsetFromStart) {
					YAHOO.util.Dom.get('contrastSliderValue').innerHTML = ui.contrastSlider.getValue() - 127;
				});
			}
		});

		ui.contrastPanel.render(document.body);
		
		// create dialog for our slider
		ui.brightnessPanel = new YAHOO.widget.Dialog("brightnessPanel", { 
				width : "23em",
				close: true,
				fixedcenter : true,
				visible : false, 
				modal: true,
				constraintoviewport : true,
				buttons : [ { text:"Apply", handler:function(){
												pixidou.applyBrightnessTool(ui.brightnessSlider.getValue() - 127);
												this.cancel();
											}, isDefault:true },
							{ text:"Cancel", handler: function(){
												this.cancel();
											}
							}]
		});
		
		// then add the slider control
        ui.brightnessPanel.renderEvent.subscribe(function(){
        	if (!ui.brightnessSlider) {
				ui.brightnessSlider = YAHOO.widget.Slider.getHorizSlider('brightnessSliderBg', 'brightnessSliderThumb', 0, 254, 1);
				ui.brightnessSlider.setValue(127);
				ui.brightnessSlider.subscribe("change", function(offsetFromStart) {
					YAHOO.util.Dom.get('brightnessSliderValue').innerHTML = ui.brightnessSlider.getValue() - 127;
				});
			}
		});

		ui.brightnessPanel.render(document.body);
		
		// create our buttons in the footer
		var pushButtonApply = new YAHOO.widget.Button("applyTool");
		var pushButtonCancel = new YAHOO.widget.Button("cancelTool");
		var pushButtonZoomIn = new YAHOO.widget.Button("zoomInTool");
		var pushButtonZoomOut = new YAHOO.widget.Button("zoomOutTool");
		
		pushButtonZoomIn.on('click', pixidou.zoomIn);
		pushButtonZoomOut.on('click', pixidou.zoomOut);
		
		// for IE to disable flickering
		try{
			document.execCommand('BackgroundImageCache', false, true);
		}
		catch(e){};
	},
	
	showUploadForm: function(){
		ui.uploadForm.show();
	},
	
	hideUploadForm: function(){
		ui.uploadForm.hide();
	},
	
	showTintPanel: function(){
		ui.tintPickerPanel.show();
	},
	
	hideTintPanel: function(){
		ui.tintPickerPanel.hide();
	},
	
	showContrastPanel: function(){
		ui.contrastPanel.show();
	},
	
	hideContrastPanel: function(){
		ui.contrastPanel.hide();
	},
	
	showBrightnessPanel: function(){
		ui.brightnessPanel.show();
	},
	
	hideBrightnessPanel: function(){
		ui.brightnessPanel.hide();
	},
	
	showLoadingPanel: function(){
		ui.loadingPanel.show();
	},
	
	hideLoadingPanel: function(){
		ui.loadingPanel.hide();
	},
	
	showAboutPanel: function(){
		ui.aboutPanel.show();
	},
	
	enableApplyButton: function(){
		// listeners will be handled by calling functions
		YAHOO.util.Dom.setStyle('applyTool', 'display', 'block');
		
		// Also enable the cancel button
		YAHOO.util.Dom.setStyle('cancelTool', 'display', 'block');
	},
	
	disableApplyButton: function(){
		YAHOO.util.Dom.setStyle('applyTool', 'display', 'none');
		YAHOO.util.Dom.setStyle('cancelTool', 'display', 'none');
		
		//remove any listeners
		YAHOO.util.Event.removeListener('applyTool', 'click');
		YAHOO.util.Event.removeListener('cancelTool', 'click');
		
		// set current crop tool to null
		pixidou.currentTool = null;
	},
	
	showSavePanel: function(){
		/**var saveDialog = new YAHOO.widget.SimpleDialog("saveDialog", 
			 { width: "300px",
			   fixedcenter: true,
			   visible: false,
			   draggable: false,
			   close: true,
			   text: "Changes have not been applied. Do you want to save them?",
			   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
			   constraintoviewport: true,
			   buttons: [ { text:"Yes", handler:function(){
			   	// get current tool
			   	if(pixidou.currentTool == 'crop'){
			   		pixidou.applyCropTool();
			   	}
			   	else if(pixidou.currentTool == 'resize'){
			   		pixidou.applyResizeTool();
			   	}
			   	this.hide();
			   }, isDefault:true },
						  { text:"No",  handler:function(){
					if(pixidou.currentTool == 'crop'){
			   			pixidou.disableCropTool();
				   	}
				   	else if(pixidou.currentTool == 'resize'){
				   		pixidou.disableResizeTool();
				   	}
				   	this.hide();	  
						  } } ]
			 } );
			 saveDialog.setHeader('Do you want to continue ?');
		saveDialog.render(document.body);
		saveDialog.show();*/
		if(confirm("Changes have not been saved. Save them ?")){
			if(pixidou.currentTool == 'crop'){
		   		pixidou.applyCropTool();
		   	}
		   	else if(pixidou.currentTool == 'resize'){
		   		pixidou.applyResizeTool();
		   	}
		}
		else{
			if(pixidou.currentTool == 'crop'){
	   			pixidou.disableCropTool();
		   	}
		   	else if(pixidou.currentTool == 'resize'){
		   		pixidou.disableResizeTool();
		   	}
		   	pixidou.savedState = true;
		}
	},
	
	initMenuBar: function(){
		// our top menu
		var navMenuBar = new YAHOO.widget.MenuBar("nav-menu", { autosubmenudisplay: true, lazyload: true});
		navMenuBar.render();
	}
};

// custom alert
window.alert = function(text){
	ui.dialogInfo.setBody(text);
	ui.dialogInfo.show();
};

				