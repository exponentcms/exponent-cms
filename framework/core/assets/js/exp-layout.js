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

var configPanel = function(title, leftct, centerct, width, height) {
	this.title = title;
	this.leftct = leftct;
	this.centerct = centerct;
	this.width = width;
	this.height = height;
};

configPanel.prototype = {

	fire: function() {
//        var YAHOO = YUI.YUI2;  //We have to get YUI from somewhere
	    var Dom = YAHOO.util.Dom,
		Event = YAHOO.util.Event,
		layout = null,
		resize = null;

	    Event.onDOMReady(function() {
		// Setup constants

		// QUIRKS FLAG, FOR BOX MODEL
		var IE_QUIRKS = (YAHOO.env.ua.ie && document.compatMode == "BackCompat");

		// UNDERLAY/IFRAME SYNC REQUIRED
		var IE_SYNC = (YAHOO.env.ua.ie == 6 || (YAHOO.env.ua.ie == 7 && IE_QUIRKS));

		// PADDING USED FOR BODY ELEMENT (Hardcoded for example)
		var PANEL_BODY_PADDING = (10*2) // 10px top/bottom padding applied to Panel body element. The top/bottom border width is 0

		var panel = new YAHOO.widget.Panel('demo', {
		    draggable: true,
		    close: true,
		    underlay: 'none',
		    width: this.width+'px',
		    xy: [100, 100]
		});

		panel.setHeader(this.title);
		panel.setBody('<div id="layout"></div>');
		panel.beforeRenderEvent.subscribe(function() {
		    Event.onAvailable('layout', function() {
		        layout = new YAHOO.widget.Layout('layout', {
		            height: this.height,
		            width: this.width - 20,
		            units: [
		                //{ position: 'top', height: 25, resize: false, body: 'Top', gutter: '2' },
		                { position: 'left', width: 200, resize: true, body: this.leftct, gutter: '0 5 0 2', minWidth: 150, maxWidth: 300 },
		                { position: 'bottom', height: 25, body: 'Bottom', gutter: '2' },
		                { position: 'center', body: this.centerct, gutter: '0 2 0 0' }
		            ]
		        });

		        layout.render();
		    }, this, true);
		}, this, true);
		panel.render();
		resize = new YAHOO.util.Resize('demo', {
		    handles: ['br'],
		    autoRatio: true,
		    status: false,
		    minWidth: 380,
		    minHeight: 400
		});
		resize.on('resize', function(args) {
		    var panelHeight = args.height;
				var headerHeight = this.header.offsetHeight; // Content + Padding + Border
				var bodyHeight = (panelHeight - headerHeight);
				var bodyContentHeight = (IE_QUIRKS) ? bodyHeight : bodyHeight - PANEL_BODY_PADDING;

				YAHOO.util.Dom.setStyle(this.body, 'height', bodyContentHeight + 'px');

				if (IE_SYNC) {
					// Keep the underlay and iframe size in sync.

					// You could also set the width property, to achieve the 
					// same results, if you wanted to keep the panel's internal
					// width property in sync with the DOM width. 

					this.sizeUnderlay();

					// Syncing the iframe can be expensive. Disable iframe if you
					// don't need it.

					this.syncIframe();
				}

		    layout.set('height', bodyContentHeight);
		    layout.set('width', (args.width - PANEL_BODY_PADDING));
		    layout.resize();
		    
		}, panel, true);
	    }, this, true);
	}
}
