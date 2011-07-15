/**
*	Pixidou - Open Source AJAX Image Editor
*	Layout stuff here
*/
layout = {
	init: function(){
		var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event;

	    Event.onDOMReady(function() {
	        var layout = new YAHOO.widget.Layout('doc3', {
	            height: Dom.getClientHeight(), //Height of the viewport
	            width: Dom.get('doc3').offsetWidth, //Width of the outer element
	            minHeight: 150, //So it doesn't get too small
	            units: [
	                { position: 'top', height: 30, body: 'hd',scroll: null, zIndex: 2, gutter: '0 0 10px 0'},
	                { position: 'bottom', height: 60, body: 'ft', collapse: true },
	                { position: 'center', body: 'bd', grids: true, scroll: true, zIndex: 1}
	            ]
	        });
	        layout.on('beforeResize', function() {
	            Dom.setStyle('doc3', 'height', Dom.getClientHeight() + 'px');
	        });
	        layout.on('render', function() {
	        	YAHOO.util.Event.onContentReady("nav-menu", ui.initMenuBar);
	        });
	
	        layout.render();
	
	        //Handle the resizing of the window
	        Event.on(window, 'resize', layout.resize, layout, true);
	    });
		
	}
};