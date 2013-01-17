{*
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
 *}

{css unique="nav-manager" link="`$asset_path`css/nav-manager.css" corecss="forms,panels,tree"}

{/css}

<div class="module navigation manager-hierarchy">
	<div class="form_header">
		<p>
            <strong>{'Drag and drop'|gettext}</strong> {'tree items using the 4-way arrows icon to re-order the site hierarchy (main menu).'|gettext}
            <ul>
                <li>{'Dropping an item on a name (name grays out) places it within that menu.'|gettext}</li>
                <li>{'Dropping an item between names (shows a line) places it next to that menu.'|gettext}</li>
            </ul>
            <strong>{'Right click on a tree item'|gettext}</strong> {'for a context menu of options.'|gettext}
        </p>
	</div>
	{permissions}
		{if $user->is_admin || $user->is_acting_admin}
			<a class="add" href="{link action=add_section parent='0'}">{'Create a New Top Level Page'|gettext}</a>
		{/if}
	{/permissions}
	{*<a id="expand" href="#">Expand all</a>*}
	<div><a id="collapse" href="#">Collapse all</a></div>
	<div id="navtree">
		<img src="{$smarty.const.ICON_RELATIVE|cat:'ajax-loader.gif'}">	<strong>Loading Navigation</strong>
	</div>
</div>

    {*FIXME convert to yui3*}
{script yui3mods="1" unique="DDTreeNav" }
{literal} 

YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-treeview','yui2-menu','yui2-animation','yui2-dragdrop','yui2-json','yui2-container','yui2-connection', function(Y) {
var YAHOO = Y.YUI2;    

//////////////////////////////////////////////////////////////////////////////
// dragdrop
//////////////////////////////////////////////////////////////////////////////
	
	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;
	var DDM = YAHOO.util.DragDropMgr;

	var DDSend = function(id, sGroup, config) {
		//Y.log(id)
		if (id) {
			new YAHOO.util.DDTarget("addafter"+id,sGroup);
			new YAHOO.util.DDTarget("addbefore"+id,sGroup);
			// bind this drag drop object to the
			// drag source object
			this.init("section"+id, sGroup, config);
			this.initFrame();
			this.setHandleElId("draghandle"+id);
		}
	};

	DDSend.prototype = new YAHOO.util.DDProxy();

	DDSend.prototype.startDrag = function(x, y) {
		var proxy = this.getDragEl();
		var real = this.getEl();
		var nodebeingdragged = tree.getNodeByElement(YAHOO.util.Dom.get(real.id));
		nodebeingdragged.collapse();
		//Y.log(Dom.get(real.id.replace("section","sectionlabel")).innerHTML);
		proxy.innerHTML = "<div class='shrinkwrap'><div id='dropindicator' class='nodrop'>&#160;</div><span>"+Dom.get(real.id.replace("section","sectionlabel")).innerHTML+"</span><span class='pshadow'></span></div>";
		YAHOO.util.Dom.addClass(real,"ghost");
		YAHOO.util.Dom.addClass(proxy,"ddnavproxiebeingdragged");
		//YAHOO.util.Dom.setStyle(proxy,"width",YAHOO.util.Dom.getStyle(proxy,"width")+"px");
		//Y.log(YAHOO.util.Dom.getStyle(proxy,"width"));
		YAHOO.util.Dom.setStyle(proxy,"border","0");
		DDM.refreshCache();

	};

	DDSend.prototype.onDragEnter = function(e, id) {
		var srcEl = this.getEl();
		var destEl = Dom.get(id);
		var dragSecId = srcEl.getAttribute("id").replace("section","");
		var hoveredSecId = id.replace("addafter","");
		hoveredSecId = hoveredSecId.replace("addbefore",""); 
		
		//Y.log('hover - '+dragSecId+' over - '+hoveredSecId);
		
		if (YAHOO.util.Dom.hasClass(destEl,"addbefore") && dragSecId!=hoveredSecId){
			YAHOO.util.Dom.addClass(destEl,"addbefore-h");
			YAHOO.util.Dom.get("dropindicator").className ="dropattop";
		}
		if (YAHOO.util.Dom.hasClass(destEl,"addafter") && dragSecId!=hoveredSecId){
			YAHOO.util.Dom.addClass(destEl,"addafter-h");
			YAHOO.util.Dom.get("dropindicator").className ="putinbetween";
		}
		if (YAHOO.util.Dom.hasClass(destEl,"lastonthelist") && dragSecId!=hoveredSecId){
			YAHOO.util.Dom.addClass(destEl,"addafter-h");
			YAHOO.util.Dom.get("dropindicator").className ="dropatbottom";
		}
		if (YAHOO.util.Dom.hasClass(destEl,"draggables")){
			YAHOO.util.Dom.addClass(destEl,"hovered");
			YAHOO.util.Dom.get("dropindicator").className ="addtome";
		}
	};

	DDSend.prototype.onDragOut = function(e, id) {
		var srcEl = this.getEl();
		var destEl = Dom.get(id);
		YAHOO.util.Dom.get("dropindicator").className ="nodrop";
		YAHOO.util.Dom.removeClass(destEl,"addbefore-h");
		YAHOO.util.Dom.removeClass(destEl,"addafter-h");
		YAHOO.util.Dom.removeClass(destEl,"hovered");
	}

	DDSend.prototype.onDragDrop = function(e, id) {
		var srcEl = this.getEl();
		var destEl = Dom.get(id);

		var dragSecId = srcEl.getAttribute("id").replace("section","");
		var hoveredSecId = id.replace("addafter","");
		hoveredSecId = hoveredSecId.replace("addbefore","");
		
		
		var draggedNode = tree.getNodeByElement(YAHOO.util.Dom.get(this.id));
		var droppedOnNode = tree.getNodeByElement(YAHOO.util.Dom.get(id));

		if (YAHOO.util.Dom.hasClass(destEl,"addbefore") && dragSecId!=hoveredSecId){
			insertBeforeNode(draggedNode,droppedOnNode);
			YAHOO.util.Dom.removeClass(destEl,"addbefore-h");
		}
		if (YAHOO.util.Dom.hasClass(destEl,"addafter") && dragSecId!=hoveredSecId){
			insertAfterNode(draggedNode,droppedOnNode);
			YAHOO.util.Dom.removeClass(destEl,"addafter-h");
		}
		if (YAHOO.util.Dom.hasClass(destEl,"draggables")){
			appendToNode(draggedNode,droppedOnNode);
			YAHOO.util.Dom.removeClass(destEl,"hovered");
		}
	}

	DDSend.prototype.endDrag = function(e) {
		var proxy = this.getDragEl();
		var real = this.getEl();

		Dom.setStyle(proxy, "visibility", "");
		var a = new YAHOO.util.Motion( 
			proxy, { 
				points: { 
					to: Dom.getXY(real)
				}
			}, 
			0.2, 
			YAHOO.util.Easing.easeOut 
		)
		
		var proxyid = proxy.id;
		var thisid = this.id;

		// Hide the proxy and show the source element when finished with the animation
		a.onComplete.subscribe(function() {
				Dom.setStyle(proxyid, "visibility", "hidden");
				Dom.setStyle(thisid, "visibility", "");
				YAHOO.util.Dom.removeClass(real,"ghost");
				YAHOO.util.Dom.removeClass(proxy,"ddnavproxiebeingdragged");
			});
		a.animate();
	}
	
	refreshDD = function () {
		dds = YAHOO.util.Dom.getElementsByClassName("draggables");
		//Y.log(dds);
		for (dd in dds){
			//Y.log(dd.getAttribute("id"));
			new DDSend(dds[dd].getAttribute("id").replace("section",""));
		}
	}
	
//////////////////////////////////////////////////////////////////////////////
// tree
//////////////////////////////////////////////////////////////////////////////
	var tree, currentIconMode;
	var usr = {/literal}{obj2json obj=$user}{literal}; //user

	ddarray = new Array;

	function changeIconMode() {
		var newVal = parseInt(this.value);
		if (newVal != currentIconMode) {
			currentIconMode = newVal;
		}
		buildTree();
		tree.getRoot().refresh();
	}

	function insertAfterNode(moveMe,moveMeAfter) {
		if(moveMe.data.id!=moveMeAfter.data.id){
			tree.popNode(moveMe);
			moveMe.insertAfter(moveMeAfter);
			saveToDB(moveMe.data.id,moveMeAfter.data.id,"after");
			tree.getRoot().refresh();
			var lotl = Dom.getElementsByClassName("lastonthelist");
			//Y.log(lotl);
		}
	}
	
	function appendToNode(moveMe,moveMeUnder) {
		if(moveMe.data.id!=moveMeUnder.data.id){
			saveToDB(moveMe.data.id,moveMeUnder.data.id,"append");
			tree.popNode(moveMe);
			//moveMeUnder.expand();
			//tree.subscribe("expand",moveMeUnder.expand);
			if (moveMeUnder.dynamicLoadComplete==true){			
				if(moveMeUnder.children[0]){
					moveMe.insertBefore(moveMeUnder.children[0]);
				} else {
					moveMe.appendTo(moveMeUnder);
				}
			}
			var lotl = Dom.getElementsByClassName("lastonthelist");
			//adjustFirstLast(moveMe,moveMeUnder);
			tree.getRoot().refresh();
		}
	}
	
	function addTopNode (){
		window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=add_section&parent=0";
	}

	function addSubNode (){
		window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=add_section&parent="+currentMenuNode.data.id;
	}
	
	function addContentSubNode (){
		window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_contentpage&parent="+currentMenuNode.data.id;
	}

	function addExternalSubNode (){
		window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_externalalias&parent="+currentMenuNode.data.id;
	}

	function addInternalSubNode (){
		window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_internalalias&parent="+currentMenuNode.data.id;
	}

	function addStandaloneSubNode (){
		window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=move_standalone&parent="+currentMenuNode.data.id;
	}

	function viewNode (){
		window.location=eXp.PATH_RELATIVE+"index.php?section="+currentMenuNode.data.id;
	}
	
	function editNode (){
		if (currentMenuNode.data.obj.alias_type==0){
			window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_contentpage&id="+currentMenuNode.data.id;
		} else if (currentMenuNode.data.obj.alias_type==1){
			window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_externalalias&id="+currentMenuNode.data.id;
        } else if (currentMenuNode.data.obj.alias_type==3){
            window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_freeform&id="+currentMenuNode.data.id;
		} else {
			window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=edit_internalalias&id="+currentMenuNode.data.id;
		}
	}
	
	function deleteNode (){
		var handleYes = function() {
			this.hide();
            if (currentMenuNode.data.obj.alias_type == 0)
			    window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=remove&id="+currentMenuNode.data.id;
            else
                window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=delete&id="+currentMenuNode.data.id;
		};
		var handleNo = function() {
			this.hide();
		};
        if (currentMenuNode.data.obj.alias_type == 0) {
		    var message = "{/literal}{"Deleting a content page moves it to the Standalone Page Manager, removing it from the Site Hierarchy. If there are any sub-pages to this section, those will also be moved"|gettext}{literal}";
            var yesbtn = "{/literal}{"Move to Standalone"|gettext}{literal}";
        } else {
            var message = "{/literal}{"Deleting an internal alias page or external link page permanently removes it from the system."|gettext}{literal}";
            var yesbtn = "{/literal}{"Delete Page"|gettext}{literal}";
        }

		// Instantiate the Dialog
		var delpage = new YAHOO.widget.SimpleDialog("simpledialog1",
										{ width: "400px",
											fixedcenter: true,
											visible: false,
											modal: true,
											draggable: false,
											close: true,
											text: message,
											icon: YAHOO.widget.SimpleDialog.ICON_HELP,
											constraintoviewport: true,
											buttons: [ { text:yesbtn, handler:handleYes, isDefault:true },
												{ text:"{/literal}{"Cancel"|gettext}{literal}",  handler:handleNo } ]
										} );
		delpage.setHeader("Remove \""+currentMenuNode.data.name+"\" from hierarchy");
		
		// Render the Dialog
		delpage.render(document.body);
		delpage.show();
	}

	function editUserPerms (){
		{/literal} {if ($smarty.const.SEF_URLS == 1)} {literal}
			window.location=eXp.PATH_RELATIVE+"navigation/userperms/int/"+currentMenuNode.data.id+"/_common/1";
		{/literal} {else} {literal}
			window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=userperms&int="+currentMenuNode.data.id+"&_common=1";
		{/literal} {/if} {literal}
	}

	function editGroupPerms (){
		{/literal} {if ($smarty.const.SEF_URLS == 1)} {literal}
			window.location=eXp.PATH_RELATIVE+"navigation/groupperms/int/"+currentMenuNode.data.id+"/_common/1";
		{/literal} {else} {literal}
			window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=groupperms&int="+currentMenuNode.data.id+"&_common=1";
		{/literal} {/if} {literal}
	}

	function saveToDB(move,target,type) {
		var iUrl = eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=navigation&action=DragnDropReRank";
		YAHOO.util.Connect.asyncRequest('POST', iUrl, 
		{
			success : function (o){
				refreshDD();
			},
			failure : function(o){
				
			},
			timeout : 50000
		},"move="+move+"&target="+target+"&type="+type);
	}

	function loadNodeData(node, fnLoadComplete)	 {
		var nodeid = encodeURI(node.data.id);
		var sUrl = eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=navigation&action=returnChildrenAsJSON&id=" + nodeid;
		var callback = {
			success: function(oResponse) {
				var oResults = YAHOO.lang.JSON.parse(oResponse.responseText);
				if((oResults.data) && (oResults.data.length)) {
					//Result is an array if more than one result, string otherwise
					if(YAHOO.lang.isArray(oResults.data)) {
						for (var i=0, j=oResults.data.length; i<j; i++) {
							var tempNode = new YAHOO.widget.HTMLNode({obj:oResults.data[i],id:oResults.data[i].id,name:oResults.data[i].name,html:buildHTML(oResults.data[i])}, node, false, true)
						}
					} else {
						//there is only one result; comes as string:
						var tempNode = new YAHOO.widget.HTMLNode({obj:oResults.data,id:oResults.data.id,name:oResults.data.name,html:buildHTML(oResults.data)}, node, false, true)
					}
				}
				//refresh DragDrop Cache
				refreshDD();
				oResponse.argument.fnLoadComplete();
			},
			failure: function(oResponse) {
				YAHOO.log("Failed to process XHR transaction.", "info", "example");
				oResponse.argument.fnLoadComplete();
			},
			argument: {
				"node": node,
				"fnLoadComplete": fnLoadComplete
			},timeout: 7000
		};
		YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
	}

	function buildTree(topnav) {
		//create a new tree:
		tree = new YAHOO.widget.TreeView("navtree");

		//turn dynamic loading on for entire tree:
		tree.setDynamicLoad(loadNodeData, currentIconMode);

		//get root node for tree:
		var root = tree.getRoot();

		for (var i=0, j=topnav.length; i<j; i++) {
			var tempNode = new YAHOO.widget.HTMLNode({obj:topnav[i],id:topnav[i].id,name:topnav[i].name,html:buildHTML(topnav[i])}, root, false, true);
		}

		tree.createEvent("nodemoved");
		tree.subscribe("expandComplete",refreshDD);
		tree.subscribe("collapseComplete",refreshDD);
		tree.subscribe("nodemoved",refreshDD);
	   
		tree.draw();
		refreshDD();

		//handler for expanding all nodes, does NOT work for dynamic nodes
		YAHOO.util.Event.on("expand", "click", function(e) {
			tree.expandAll();
			YAHOO.util.Event.preventDefault(e);
		});

		//handler for collapsing all nodes
		YAHOO.util.Event.on("collapse", "click", function(e) {
			tree.collapseAll();
			YAHOO.util.Event.preventDefault(e);
		});
	}
	
	function buildHTML(section) {
		var last = (section.last==true)?'lastonthelist':'';
		var draggable = (section.manage!=false)? 'draggables' : 'nondraggables' ;
		var dragafters = (section.manage!=false)? 'addafter' : 'cannotaddafter' ;
        if (section.parent==0 && usr.is_acting_admin!=1 && usr.is_admin!=1) dragafters = 'cannotaddafter' ;
        if (section.alias_type == 0) atype = ' addpage';
        else if (section.alias_type == 1) atype = ' addextpage';
        else if (section.alias_type == 2) atype = ' addintpage';
        else if (section.alias_type == 3) atype = ' addfreeform';
		//var dragbefores = (section.manage!=false)? 'addbefore' : 'cannotaddbefore' ;
		//var first = (section.rank==0)?'<div class="'+dragbefores+'" id="addbefore'+section.id+'"></div>':'';
		var drag = (section.manage!=false)?'<div class="draghandle" id="draghandle'+section.id+'">&#160;</div>':'';
        if (section.active == 1) {
            activeclass = '';
        } else {
            activeclass = ' inactive';
        }
		var html = '<div class="'+draggable+'" id="section'+section.id+'">'+drag+'<a href="'+section.link+'"><span class="sectionlabel'+activeclass+atype+'" id="sectionlabel'+section.id+'">'+section.name+'</span></a></div><div class="'+dragafters+' '+last+'" id="addafter'+section.id+'"></div>';
		return html;
	}
	
	function initTree (){
		var sUrl = eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=navigation&action=returnChildrenAsJSON&id="+0;
		var callback = {
			success: function(oResponse) {
				var oResults = YAHOO.lang.JSON.parse(oResponse.responseText);
				buildTree(oResults.data);
			},
			failure: function(oResponse) {
				YAHOO.log("Failed to process XHR transaction.", "info", "example");
			},
			timeout: 7000,
			scope: callback
		};
		YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
	}

	// context menu 
	var currentMenuNode = null;
	
	function onTriggerContextMenu(p_oEvent) {
		var theID = this.contextEventTarget;
        if(YAHOO.util.Dom.getAncestorByClassName(theID,"nondraggables")){
            this.cancel();
        } else if(YAHOO.util.Dom.hasClass(theID,"sectionlabel")){
			currentMenuNode = tree.getNodeByElement(theID);
			oContextMenu.setItemGroupTitle(currentMenuNode.data.name,0);
		} else {
			this.cancel();
		}
	}
	
	if (usr.is_acting_admin==1 || usr.is_admin==1) {
		var navoptions = [
				{ classname:"addsubpage", text: "{/literal}{"Add A Subpage"|gettext}{literal}", onclick: { fn: addSubNode },
					submenu: {
						id: "submenu1",
						itemdata: [
							{ classname:"addpage", text: "{/literal}{"Add Content Page Here"|gettext}{literal}", onclick: { fn: addContentSubNode } },
							{ classname:"addextpage", text: "{/literal}{"Add External Website Link (Page) Here"|gettext}{literal}", onclick: { fn: addExternalSubNode } },
							{ classname:"addintpage", text: "{/literal}{"Add Page Alias (Page) Here"|gettext}{literal}", onclick: { fn: addInternalSubNode } },
							{ classname:"addsapage", text: "{/literal}{"Move Standalone Page to Here"|gettext}{literal}", onclick: { fn: addStandaloneSubNode } }
						]
					}
				},
				{ classname:"viewpage", text: "{/literal}{"View This Page"|gettext}{literal}", onclick: { fn: viewNode } },
				{ classname:"editpage", text: "{/literal}{"Edit This Page"|gettext}{literal}", onclick: { fn: editNode } },
				{ classname:"deletepage", text: "{/literal}{"Delete This Page"|gettext}{literal}", onclick: { fn: deleteNode } },
				{ classname:"userperms", text: "{/literal}{"Manage User Permissions"|gettext}{literal}", onclick: { fn: editUserPerms } },
				{ classname:"groupperms", text: "{/literal}{"Manage Group Permissions"|gettext}{literal}", onclick: { fn: editGroupPerms } }
			];
	} else {
		var navoptions = [
				{ classname:"addsubpage", text: "{/literal}{"Add A Subpage"|gettext}{literal}", onclick: { fn: addSubNode },
					submenu: {
						id: "submenu1",
						itemdata: [
							{ classname:"addpage", text: "{/literal}{"Add Content Page Here"|gettext}{literal}", onclick: { fn: addContentSubNode } },
							{ classname:"addextpage", text: "{/literal}{"Add External Website Link (Page) Here"|gettext}{literal}", onclick: { fn: addExternalSubNode } },
							{ classname:"addintpage", text: "{/literal}{"Add Page Alias (Page) Here"|gettext}{literal}", onclick: { fn: addInternalSubNode } }
						]
					}
				},
				{ classname:"viewpage", text: "{/literal}{"View This Page"|gettext}{literal}", onclick: { fn: viewNode } },
				{ classname:"editpage", text: "{/literal}{"Edit This Page"|gettext}{literal}", onclick: { fn: editNode } },
				{ classname:"deletepage", text: "{/literal}{"Delete This Page"|gettext}{literal}", onclick: { fn: deleteNode } }
			];
	}

	var oContextMenu = new YAHOO.widget.ContextMenu("navTreeContext", {
																	trigger: "navtree",
																	hidedelay:1000,
																	zIndex:500,
																	classname: "yui-skin-sam",
																	itemdata:navoptions,
																	lazyload: true,
																	autosubmenudisplay: true
																	 });
	oContextMenu.subscribe("triggerContextMenu", onTriggerContextMenu); 

	DDSend.init = function() {
		YAHOO.util.Event.on(["mode0", "mode1"], "click", changeIconMode);
		var el = document.getElementById("mode1");
		if (el && el.checked) {
			currentIconMode = parseInt(el.value);
		} else {
			currentIconMode = 0;
		}

		initTree();
	}
	
    DDSend.init();
//once the DOM has loaded, we can go ahead and set up our tree:

});

{/literal}
{/script}
