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

(function() {
YAHOO.namespace('container');
YAHOO.namespace('container.conModule');
var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var DDM = YAHOO.util.DragDropMgr;

//////////////////////////////////////////////////////////////////////////////
// example app
//////////////////////////////////////////////////////////////////////////////
YAHOO.example.DDApp = {

    init: function() {

		//find all contaners with a dragablecontainer class
        var ddc = Dom.getElementsByClassName('dragablecontainer') ;
		
		// loop through DragDrop Containers, assign them to DDTarget based on their ID attribute
		var moduleCounter = 0;
		YAHOO.container.conModule = new Array();
		for ( i=0; i<ddc.length; i++ ) {
			
			//grab contianer ID
			var container = ddc[i].getAttribute('id');
			//alert(container);
			//assign that container as a drop zone
            new YAHOO.util.DDTarget(container);
			
			// grab all dragable modules in the container, based off class name "dragablemodule"
			var ddm = Dom.getElementsByClassName('dragablemodule', 'div', container);
			//alert(ddm);
            for ( j=0; j<ddm.length; j++ ) {
				
				//Grab module ID
				//var idString = i+'_'+j;
				ddmID = ddm[j].getAttribute('id');
				
				//assign module to list of dragable items
                new YAHOO.example.DDList(ddmID);
				
				//YAHOO.example.DDList.setHandleElId(Dom.getElementsByClassName('draghandle', 'div', ddmID)[0].getAttribute('id'));
				
				//Get handle based off 'draghandle' class, then grab it's ID to use for assigning as drag handle
				//	var handleID = Dom.setStyle(Dom.getElementsByClassName('draghandle', 'div', ddmID)[0].getAttribute('id'),'backgroundColor','#994444');

				//invalidHandleIds
				//assign the handle 
				YAHOO.container.conModule[moduleCounter] = ddmID;
				//update counter
				moduleCounter++;
				//alert(module);
				
            }
			
			//
          // dd3 = new YAHOO.util.DDTarget("dragDiv3");

       }

        Event.on("showButton", "click", this.showOrder);
        Event.on("switchButton", "click", this.switchStyles);
		
    },
	
	isInArray : function ( id ) {
		var len = YAHOO.container.conModule.length;
		for ( var x = 0 ; x <= len ; x++ ) {
			if ( YAHOO.container.conModule[x] == id ) return true;
		}
		return false;
	},

    showOrder: function() {
        var parseList = function(ul, title) {
            var items = ul.getElementsByTagName("li");
            var out = title + ": ";
            for (i=0;i<items.length;i=i+1) {
                out += items[i].id + " ";
            }
            return out;
        };

        var ul1=Dom.get("ul1"), ul2=Dom.get("ul2");
        alert(parseList(ul1, "List 1") + "\n" + parseList(ul2, "List 2"));

    },

    switchStyles: function() {
        Dom.get("ul1").className = "draglist_alt";
        Dom.get("ul2").className = "draglist_alt";
    }
};

//////////////////////////////////////////////////////////////////////////////
// custom drag and drop implementation
//////////////////////////////////////////////////////////////////////////////

YAHOO.example.DDList = function(id, sGroup, config) {

    YAHOO.example.DDList.superclass.constructor.call(this, id, sGroup, config);
	
	this.setHandleElId(Dom.getElementsByClassName('draghandle', 'div', id)[0].getAttribute('id'))
	
    this.logger = this.logger || YAHOO;
    var el = this.getDragEl();
    Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent

    this.goingUp = false;
    this.lastY = 0;
};

YAHOO.extend(YAHOO.example.DDList, YAHOO.util.DDProxy, {

    startDrag: function(x, y) {
        this.logger.log(this.id + " startDrag");

        // make the proxy look like the source element
        var dragEl = this.getDragEl();
        var clickEl = this.getEl();
        Dom.setStyle(clickEl, "visibility", "hidden");

        dragEl.innerHTML = clickEl.innerHTML;

        Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
        Dom.setStyle(dragEl, "border", "2px solid gray");
    },

    endDrag: function(e) {

        var srcEl = this.getEl();
        var proxy = this.getDragEl();

        // Show the proxy element and animate it to the src element's location
        Dom.setStyle(proxy, "visibility", "");
        var a = new YAHOO.util.Motion( 
            proxy, { 
                points: { 
                    to: Dom.getXY(srcEl)
                }
            }, 
            .25, 
            YAHOO.util.Easing.easeOut 
        )
        var proxyid = proxy.id;
        var thisid = this.id;

        // Hide the proxy and show the source element when finished with the animation
        a.onComplete.subscribe(function() {
                Dom.setStyle(proxyid, "visibility", "hidden");
                Dom.setStyle(thisid, "visibility", "");
            });
        a.animate();
    },

    onDragDrop: function(e, id) {

        // If there is one drop interaction, the li was dropped either on the list,
        // or it was dropped on the current location of the source element.
        if (DDM.interactionInfo.drop.length === 1) {

            // The position of the cursor at the time of the drop (YAHOO.util.Point)
            var pt = DDM.interactionInfo.point; 

            // The region occupied by the source element at the time of the drop
            var region = DDM.interactionInfo.sourceRegion; 

            // Check to see if we are over the source element's location.  We will
            // append to the bottom of the list once we are sure it was a drop in
            // the negative space (the area of the list without any list items)
            if (!region.intersect(pt)) {
                var destEl = Dom.get(id);
                var destDD = DDM.getDDById(id);
                destEl.appendChild(this.getEl());
                destDD.isEmpty = false;
                DDM.refreshCache();
            }

        }
    },

    onDrag: function(e) {

        // Keep track of the direction of the drag for use during onDragOver
        var y = Event.getPageY(e);

        if (y < this.lastY) {
            this.goingUp = true;
        } else if (y > this.lastY) {
            this.goingUp = false;
        }

        this.lastY = y;
    },

    onDragOver: function(e, id) {
    
        var srcEl = this.getEl();
        var destEl = Dom.get(id);
		//var destElClasses = destEl.getAttribute('class').split(' ');
        // We are only concerned with list items, we ignore the dragover
        // notifications for the list.
		//alert(id);
        if (YAHOO.example.DDApp.isInArray(id)) {
            var orig_p = srcEl.parentNode;
            var p = destEl.parentNode;

            if (this.goingUp) {
                p.insertBefore(srcEl, destEl); // insert above
            } else {
                p.insertBefore(srcEl, destEl.nextSibling); // insert below
            }

            DDM.refreshCache();
        }
    }
});

YAHOO.example.DDOnTop = function(id, sGroup, config) {
    if (id) {
        this.init(id, sGroup, config);
        this.logger = this.logger || YAHOO;
    }
};

// YAHOO.example.DDOnTop.prototype = new YAHOO.util.DD();
YAHOO.extend(YAHOO.example.DDOnTop, YAHOO.util.DD);

/**
 * The inital z-index of the element, stored so we can restore it later
 *
 * @type int
 */
YAHOO.example.DDOnTop.prototype.origZ = 0;

YAHOO.example.DDOnTop.prototype.startDrag = function(x, y) {
    this.logger.log(this.id + " startDrag");

    var style = this.getEl().style;

    // store the original z-index
    this.origZ = style.zIndex;

    // The z-index needs to be set very high so the element will indeed be on top
    style.zIndex = 999;
};

YAHOO.example.DDOnTop.prototype.endDrag = function(e) {
    this.logger.log(this.id + " endDrag");

    // restore the original z-index
    this.getEl().style.zIndex = this.origZ;
};

Event.onDOMReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);

})();
