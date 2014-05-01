/*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

//FIXME convert to yui3
YUI.add('exp-tree', function(Y) {
var YAHOO = Y.YUI2;

var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var DDM = YAHOO.util.DragDropMgr;
var applicationModule;

EXPONENT.tv = YAHOO.widget.TreeView;

var refreshDD = function () {
    dds = YAHOO.util.Dom.getElementsByClassName("dragtable");
    
    for (var i=0; i<dds.length; i++ ){
        //Y.log(dds[i].id);
        new EXPONENT.DragDropTree(dds[i].id.replace("dragtable",""));
    }
}

var buildContextMenu = function(div) {

    function addSubNode (){
        window.location = eXp.PATH_RELATIVE+"index.php?controller="+applicationModule+"&action=adsubnode&id="+currentMenuNode.data.id;
    }

    function editNode (){
        window.location = eXp.PATH_RELATIVE+"index.php?controller="+applicationModule+"&action=edit&id="+currentMenuNode.data.id;
    }

    function deleteNode (){
        var handleYes = function() {
            this.hide();
            window.location = eXp.PATH_RELATIVE+"index.php?controller="+applicationModule+"&action=delete&id="+currentMenuNode.data.id;
        };
        var handleNo = function() {
            this.hide();
        };

        var message = "Are you sure you want to delete this node?";

        YAHOO.namespace("example.container");

        // Instantiate the Dialog
        YAHOO.example.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                                        { width: "400px",
                                            fixedcenter: true,
                                            visible: false,
                                            modal: true,
                                            draggable: false,
                                            close: true,
                                            text: message,
                                            icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                            constraintoviewport: true,
                                            buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
                                                { text:"Cancel",  handler:handleNo } ]
                                        } );
        YAHOO.example.container.simpledialog1.setHeader("Remove \""+currentMenuNode.data.label+"\" from tree");

        // Render the Dialog
        YAHOO.example.container.simpledialog1.render(document.body);
        YAHOO.example.container.simpledialog1.show();

    }
    function configNode (){
        window.location = eXp.PATH_RELATIVE+"index.php?controller="+applicationModule+"&action=configure&id="+currentMenuNode.data.id;
    }


    // function editUserPerms (){
    //  window.location="{/literal}{$smarty.const.PATH_RELATIVE}{literal}index.php?module=navigation&_common=1&action=userperms&int="+currentMenuNode.data.id;
    // }
    // 
    // function editGroupPerms (){
    //  window.location="{/literal}{$smarty.const.PATH_RELATIVE}{literal}index.php?module=navigation&_common=1&action=groupperms&int="+currentMenuNode.data.id;
    // }

    var currentMenuNode = null;

    function onTriggerContextMenu(p_oEvent) {
        var theID = this.contextEventTarget;
        if(YAHOO.util.Dom.hasClass(theID,"context")){
            currentMenuNode = tree.getNodeByElement(theID);
            oContextMenu.setItemGroupTitle(currentMenuNode.data.label,0);
        } else {
            this.cancel();
        }
    }

    var navoptions = [
            { classname:"addsubpage", text: "Add A Sub-Category", onclick: { fn: addSubNode } },
            { classname:"editpage", text: "Edit This Category", onclick: { fn: editNode } },
            { classname:"deletepage", text: "Delete This Category", onclick: { fn: deleteNode } },
            { classname:"configurepage", text: "Configure This Category", onclick: { fn: configNode } }
            // { classname:"userperms", text: "Manage User Permissions", onclick: { fn: editUserPerms } },
            // { classname:"groupperms", text: "Manage Group Permissions", onclick: { fn: editGroupPerms } }
        ];                                                                  


    var oContextMenu = new YAHOO.widget.ContextMenu("treecontext", {
                                                    trigger: div,
                                                    hidedelay:1000,
                                                    zIndex:500,
                                                    itemdata:navoptions,
                                                    lazyload: true
                                                     });
    oContextMenu.subscribe("triggerContextMenu", onTriggerContextMenu); 
}

EXPONENT.DragDropTree = function(id, sGroup, config) {
    //Y.log(id.replace('ygtv',''))
    if (id) {
        //new YAHOO.util.DDTarget("addafter"+id,sGroup);
        //new YAHOO.util.DDTarget("addbefore"+id,sGroup);
        // bind this drag drop object to the
        // drag source object
        this.init("dragtable"+id, sGroup, config);
        this.initFrame();
        this.setHandleElId("nodeDragHandle"+id);
        this.resizeFrame = false;
    }
};

YAHOO.extend(EXPONENT.DragDropTree, YAHOO.util.DDProxy, {
    startDrag: function(x, y) {
        var proxy = this.getDragEl();
        var real = this.getEl();
        var nodebeingdragged = tree.getNodeByElement(YAHOO.util.Dom.get(real.id));
        nodebeingdragged.collapse();
        
        var draglabel = YAHOO.util.Dom.get(real.id.replace("dragtable","ygtvlabelel")).innerHTML;
        this.ddclassindicator = "nodrop"
        proxy.innerHTML = "<div id=\"dropindicator\" class=\""+this.ddclassindicator+"\">&#160;</div><span id=\"draglable\">"+draglabel+"</span>";
        this.setDelta(-10,-10);
    
        YAHOO.util.Dom.setStyle(proxy, 'width', 'auto');
        YAHOO.util.Dom.setStyle(proxy, 'height', 'auto');
        YAHOO.util.Dom.setStyle(proxy, 'border', '2px solid #669');
        YAHOO.util.Dom.setStyle(proxy, 'border-width', '1px 2px 2px 1px');
        YAHOO.util.Dom.setStyle(proxy, 'background', '#fff');
    
        this.destTop = [0,0];
        this.destMiddle = [0,0];
        this.destBottom = [0,0];

        YAHOO.util.Dom.addClass("dragtable"+tree.root.children[0].index, 'topoflist');
        YAHOO.util.Dom.addClass("dragtable"+tree.root.children[tree.root.children.length-1].index, 'bottomoflist');

        for (n in tree._nodes){
            if (tree._nodes[n].children.length != 0) {
                // Y.log("dragtable"+tree._nodes[n].children[0].index);
                // Y.log("dragtable"+tree._nodes[n].children[tree._nodes[n].children.length-1].index);
                YAHOO.util.Dom.addClass("dragtable"+tree._nodes[n].children[0].index, 'topoflist')
                YAHOO.util.Dom.addClass("dragtable"+tree._nodes[n].children[tree._nodes[n].children.length-1].index, 'bottomoflist')
            };
        }
    
        DDM.refreshCache();
    },

    onDrag: function(e){
        this.mousepos = YAHOO.util.Event.getXY(e);
    
        if (this.curHovEl!=0){
            if (this.mousepos[1]>=this.destTop[0] && this.mousepos[1]<=this.destTop[1]) {
                YAHOO.util.Dom.setStyle(this.curHovEl, 'background','url('+EXPONENT.ICON_RELATIVE+'/dhr.gif) repeat-x 0 0 #cee2ef');
                if(YAHOO.util.Dom.hasClass(this.curHovEl, 'topoflist')){
                    this.ddclassindicator = "dropattop"
                } else {
                    this.ddclassindicator = "putinbetween"
                }
            }
            if (this.mousepos[1]>=this.destMiddle[0] && this.mousepos[1]<=this.destMiddle[1]) {
                YAHOO.util.Dom.setStyle(this.curHovEl, 'background', '#bed1de');
                YAHOO.util.Dom.setStyle(this.curHovEl, 'background-image', 'none');
                this.ddclassindicator = "addtome"
            }
            if (this.mousepos[1]>=this.destBottom[0] && this.mousepos[1]<=this.destBottom[1]) {
                YAHOO.util.Dom.setStyle(this.curHovEl, 'background','url('+EXPONENT.ICON_RELATIVE+'/dhr.gif) repeat-x 0 100% #cee2ef');
                if(YAHOO.util.Dom.hasClass(this.curHovEl, 'bottomoflist')){
                    this.ddclassindicator = "dropatbottom"
                } else {
                    this.ddclassindicator = "putinbetween"
                }
            }
        }
        var oldclass = YAHOO.util.Dom.get('dropindicator').getAttribute("class");
        YAHOO.util.Dom.replaceClass('dropindicator',oldclass,this.ddclassindicator);
    
        //Y.log(YAHOO.util.Dom.get('dropindicator').getAttribute("class"));
    
    },

    onDragOver: function(e, id) {
        var srcEl = this.getEl();
        this.curHovEl = Dom.get(id);
    
        var destY = Math.floor(YAHOO.util.Dom.getY(this.curHovEl));
        var destHeight = parseInt(YAHOO.util.Dom.getStyle(this.curHovEl, 'height'));
        var destDivide = Math.floor(destHeight/3);
        this.destTop = [destY-1,destY+destDivide];
        this.destMiddle = [destY+destDivide+1,destY+(destDivide*2)];
        this.destBottom = [destY+(destDivide*2)+1,destY+destHeight+2];
        this.ddclassindicator = "nodrop"
    },

    onDragOut: function(e, id) {
        var srcEl = this.getEl();
        var destEl = YAHOO.util.Dom.get(id);
        YAHOO.util.Event.removeListener(window, 'mousemove');
        this.curHovEl = 0;
        // this.destTop = [0,0];
        // this.destMiddle = [0,0];
        // this.destBottom = [0,0];
        this.ddclassindicator = "nodrop"
        var oldclass = YAHOO.util.Dom.get('dropindicator').getAttribute("class");
        YAHOO.util.Dom.replaceClass('dropindicator',oldclass,this.ddclassindicator);
        YAHOO.util.Dom.setStyle(destEl, 'background', 'none');
    },

    onDragDrop: function(e, id) {
        var srcEl = this.getEl();
        var destEl = YAHOO.util.Dom.get(id);

        var dragSecId = srcEl.getAttribute("id").replace("section","");
        var draggedNode = tree.getNodeByElement(YAHOO.util.Dom.get(this.id));
        var droppedOnNode = tree.getNodeByElement(YAHOO.util.Dom.get(id));
    
        var allnodes = YAHOO.util.Dom.getElementsByClassName('dragtable', 'div');
    
        YAHOO.util.Dom.removeClass(allnodes, 'putinbetween');
        YAHOO.util.Dom.removeClass(allnodes, 'dropattop');
        YAHOO.util.Dom.removeClass(allnodes, 'dropatbottom');
        YAHOO.util.Dom.removeClass(allnodes, 'addtome');
        YAHOO.util.Dom.setStyle(id, 'background','none');
    
    
        if (this.mousepos[1]>=this.destTop[0] && this.mousepos[1]<=this.destTop[1]) {
            this.moveNode(draggedNode,droppedOnNode,"addbefore");
        }
        if (this.mousepos[1]>=this.destMiddle[0] && this.mousepos[1]<=this.destMiddle[1]) {
            this.moveNode(draggedNode,droppedOnNode,"append");
        }
        if (this.mousepos[1]>=this.destBottom[0] && this.mousepos[1]<=this.destBottom[1]) {
            this.moveNode(draggedNode,droppedOnNode,"addafter");
        }
    },

    endDrag: function(e) {
        refreshDD();
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

    },
    moveNode: function(draggedNode,droppedOnNode,type) {
        if(type=="addbefore") {
            tree.popNode(draggedNode);
            draggedNode.insertBefore(droppedOnNode);
            tree.getRoot().refresh();
        }
        
        if(type=="addafter") {
            tree.popNode(draggedNode);
            draggedNode.insertAfter(droppedOnNode);
            tree.getRoot().refresh();
        }

        if (type=="append") {
            tree.popNode(draggedNode);
            if(droppedOnNode.children[0]){
                draggedNode.insertBefore(droppedOnNode.children[0]);
            } else {
                draggedNode.appendTo(droppedOnNode);
            }
            tree.getRoot().refresh();
        }
        var iUrl = EXPONENT.PATH_RELATIVE+"index.php?ajax_action=1&controller="+applicationModule+"&action=reorder";
        YAHOO.util.Connect.asyncRequest('POST', iUrl, 
        {
            success : function (o){
                refreshDD();
            },
            failure : function(o){

            },
            timeout : 50000
        },"move="+draggedNode.data.id+"&target="+droppedOnNode.data.id+"&type="+type);
        
        refreshDD();
    }
});

YAHOO.widget.TaskNode = function(oData, oParent, expanded, checked, obj) {

    if (oData) { 
        this.init(oData, oParent, expanded);
        this.setUpLabel(oData);
        this.checked = checked;
        this.draggable = obj.draggable;
        this.checkable = obj.checkable;
        this.id = obj.id;
        this.rgt = obj.rgt;
        this.lft = obj.lft;
    }

};

YAHOO.extend(YAHOO.widget.TaskNode, YAHOO.widget.TextNode, {

     /**
      * True if checkstate is 1 (some children checked) or 2 (all children checked),
      * false if 0.
      * @type boolean
      */
     checked: false,

     /**
      * checkState
      * 0=unchecked, 1=some children checked, 2=all children checked
      * @type int
      */
     checkState: 0,

 	/**
      * The node type
      * @property _type
      * @private
      * @type string
      * @default "TextNode"
      */
     _type: "TaskNode",

 	taskNodeParentChange: function() {
         //this.updateParent();
     },

     setUpCheck: function(checked) {
         // if this node is checked by default, run the check code to update
         // the parent's display state
         if (checked && checked === true) {
             this.check();
         // otherwise the parent needs to be updated only if its checkstate 
         // needs to change from fully selected to partially selected
         } else if (this.parent && 2 === this.parent.checkState) {
              this.updateParent();
         }

         // set up the custom event on the tree for checkClick
         /**
          * Custom event that is fired when the check box is clicked.  The
          * custom event is defined on the tree instance, so there is a single
          * event that handles all nodes in the tree.  The node clicked is 
          * provided as an argument.  Note, your custom node implentation can
          * implement its own node specific events this way.
          *
          * @event checkClick
          * @for YAHOO.widget.TreeView
          * @param {YAHOO.widget.Node} node the node clicked
          */
         if (this.tree && !this.tree.hasEvent("checkClick")) {
             this.tree.createEvent("checkClick", this.tree);
         }

 		this.tree.subscribe('clickEvent',this.checkClick);
         this.subscribe("parentChange", this.taskNodeParentChange);

     },

     /**
      * The id of the check element
      * @for YAHOO.widget.TaskNode
      * @type string
      */
     getCheckElId: function() { 
         return "ygtvcheck" + this.index; 
     },

     /**
      * Returns the check box element
      * @return the check html element (img)
      */
     getCheckEl: function() { 
         return document.getElementById(this.getCheckElId()); 
     },

     /**
      * The style of the check element, derived from its current state
      * @return {string} the css style for the current check state
      */
     getCheckStyle: function() { 
         return "ygtvcheck" + this.checkState;
     },


    /**
      * Invoked when the user clicks the check box
      */
     checkClick: function(oArgs) { 
 		var node = oArgs.node;
 		var target = YAHOO.util.Event.getTarget(oArgs.event);
 		if (YAHOO.util.Dom.hasClass(target,'ygtvspacer')) {
 	        node.logger.log("previous checkstate: " + node.checkState);
 	        if (node.checkState === 0) {
 	            node.check();
 	        } else {
 	            node.uncheck();
 	        }

 	        node.onCheckClick(node);
 	        this.fireEvent("checkClick", node);
 		    return false;
 		}
     },

     /**
      * Override to get the check click event
      */
     onCheckClick: function() { 
         this.logger.log("onCheckClick: " + this);
     },

     /**
      * Refresh the state of this node's parent, and cascade up.
      */
     updateParent: function() { 
         var p = this.parent;

         if (!p || !p.updateParent) {
             this.logger.log("Abort udpate parent: " + this.index);
             return;
         }

         var somethingChecked = false;
         var somethingNotChecked = false;

         for (var i=0, l=p.children.length;i<l;i=i+1) {

             var n = p.children[i];

             if ("checked" in n) {
                 if (n.checked) {
                     somethingChecked = true;
                     // checkState will be 1 if the child node has unchecked children
                     if (n.checkState === 1) {
                         somethingNotChecked = true;
                     }
                 } else {
                     somethingNotChecked = true;
                 }
             }
         }

         if (somethingChecked) {
             p.setCheckState( (somethingNotChecked) ? 1 : 2 );
         } else {
             p.setCheckState(0);
         }

         p.updateCheckHtml();
         p.updateParent();
     },

     /**
      * If the node has been rendered, update the html to reflect the current
      * state of the node.
      */
     updateCheckHtml: function() { 
         if (this.parent && this.parent.childrenRendered) {
             this.getCheckEl().className = this.getCheckStyle();
         }
     },

     /**
      * Updates the state.  The checked property is true if the state is 1 or 2
      * 
      * @param the new check state
      */
     setCheckState: function(state) { 
         this.checkState = state;
         this.checked = (state > 0);
     },

     /**
      * Check this node
      */
     check: function() { 
         this.logger.log("check");
         this.setCheckState(2);
         for (var i=0, l=this.children.length; i<l; i=i+1) {
             var c = this.children[i];
             if (c.check) {
                 c.check();
             }
         }
         this.updateCheckHtml();
         this.updateParent();
     },

     /**
      * Uncheck this node
      */
     uncheck: function() { 
         this.setCheckState(0);
         for (var i=0, l=this.children.length; i<l; i=i+1) {
             var c = this.children[i];
             if (c.uncheck) {
                 c.uncheck();
             }
         }
         this.updateCheckHtml();
         this.updateParent();
     },
     buildInput: function(val,checked) {
         var chk = checked ? " checked" : "";
         var input = '<input id="checkform'+this.data.id+'"'+chk+' type="checkbox" name="'+applicationModule+'[]" value="'+val+'">';
         return input
     },

     // Overrides YAHOO.widget.TextNode
     getNodeHtml: function() {                                                                                                                                           
         var sb = [];
         var getNode = 'EXPONENT.tv.getNode(\'' +
                         this.tree.id + '\',' + this.index + ')';


         sb[sb.length] = '<div class="dragtable context" id="dragtable'+this.index+'"><table id="ygtvtableel' + this.index + '"border="0" cellpadding="0" cellspacing="0" class="ygtvtable ygtvdepth' + this.depth;
         if (this.enableHighlight) {
             sb[sb.length] = ' ygtv-highlight' + this.highlightState;
         }
         if (this.className) {
             sb[sb.length] = ' ' + this.className;
         }           
         sb[sb.length] = '"><tr class="ygtvrow">';

         for (var i=0;i<this.depth;++i) {
             sb[sb.length] = '<td class="ygtvcell ' + this.getDepthStyle(i) + '"><div class="ygtvspacer"></div></td>';
         }

         if (this.hasIcon) {
             sb[sb.length] = '<td id="' + this.getToggleElId();
             sb[sb.length] = '" class="ygtvcell ';
             sb[sb.length] = this.getStyle() ;
             sb[sb.length] = '"><a href="#" class="ygtvspacer" style="display:block;text-decoration:none;">&#160;</a></td>';
         }

         sb[sb.length] = '<td id="' + this.contentElId; 
         sb[sb.length] = '" class="ygtvcell ';
         sb[sb.length] = this.contentStyle  + ' ygtvcontent" ';
         sb[sb.length] = (this.nowrap) ? ' nowrap="nowrap" ' : '';
         sb[sb.length] = ' >';
         // Dragdrop
         if(this.draggable == true){
             sb[sb.length] = '<td';
             //sb[sb.length] = '';
             sb[sb.length] = ' class="nodeDragHandle"';
             //sb[sb.length] = ' onclick="javascript:' + this.getCheckLink() + '">';
             sb[sb.length] = '">';
             sb[sb.length] = '<div id="nodeDragHandle' + this.index + '" class="ygtvspacer" style="width:100%;height:100%;">&#160;</div></td>';
             //Y.log(this.getElId());
             //YAHOO.util.Dom.setStyle(this.getElId(), 'background', 'red');
         }

         // check box
         if(this.checkable == true){
             var input = this.buildInput(this.id,this.checked);
             sb[sb.length] = '<td>';
             // sb[sb.length] = ' id="' + this.getCheckElId() + '"';
             // sb[sb.length] = ' class="' + this.getCheckStyle() + '"';
             // sb[sb.length] = ' onclick="javascript:' + this.getCheckLink() + '">';
             //sb[sb.length] = ' </td>';
             sb[sb.length] = '<div class="ygtvspacer ddcheckbox">'+input+'</div></td>';
         }

         sb[sb.length] = '<td>';
         sb[sb.length] = '<a';
         sb[sb.length] = ' id="' + this.labelElId + '"';
         sb[sb.length] = ' class="' + this.labelStyle + ' context"';
         sb[sb.length] = ' href="' + this.href + '"';
         sb[sb.length] = ' target="' + this.target + '"';
         sb[sb.length] = ' onclick="return ' + getNode + '.onLabelClick(' + getNode +')"';
         if (this.hasChildren(true)) {
             sb[sb.length] = ' onmouseover="document.getElementById(\'';
             sb[sb.length] = this.getToggleElId() + '\').className=';
             sb[sb.length] = 'EXPONENT.tv.getNode(\'';
             sb[sb.length] = this.tree.id + '\',' + this.index +  ').getHoverStyle()"';
             sb[sb.length] = ' onmouseout="document.getElementById(\'';
             sb[sb.length] = this.getToggleElId() + '\').className=';
             sb[sb.length] = 'EXPONENT.tv.getNode(\'';
             sb[sb.length] = this.tree.id + '\',' + this.index +  ').getStyle()"';
         }
         sb[sb.length] = (this.nowrap) ? ' nowrap="nowrap" ' : '';
         sb[sb.length] = ' >';
         sb[sb.length] = this.label;
              
         //sb[sb.length] = this.lft+' | '+this.label+'-'+this.id+' | '+this.rgt;
         
         sb[sb.length] = '</a>';
         sb[sb.length] = '</td>';             sb[sb.length] = '</td></tr></table></div>';

         return sb.join("");

     }

});

EXPONENT.DragDropTree.init = function(div,obj,mod,menu,expandonstart) {
    applicationModule = mod;
    tree = new YAHOO.widget.TreeView(div);
    var root = tree.getRoot();
    var node = [];
    node[0]=root;
    
    for(var o=0; o<obj.length;o++){
        var parent = node[obj[o].parent_id];
        var params = {label:obj[o].title, id:obj[o].id };
        node[obj[o].id] = new YAHOO.widget.TaskNode(params, parent,expandonstart, obj[o].value, obj[o]);
    }
    
    YAHOO.util.Event.on('expandall', 'click', function(e){
        tree.unsubscribe("expandComplete",refreshDD);
        tree.expandAll();
        tree.subscribe("expandComplete",refreshDD);
        refreshDD();
    });   
    
    YAHOO.util.Event.on('collapseall', 'click', function(e){
        tree.unsubscribe("collapseComplete",refreshDD);
        tree.collapseAll();
        tree.subscribe("collapseComplete",refreshDD);
        refreshDD();
    });   
    
    tree.draw();
    refreshDD();
    tree.createEvent("nodemoved");
    tree.subscribe("expandComplete",refreshDD);
    tree.subscribe("collapseComplete",refreshDD);
    tree.subscribe("nodemoved",refreshDD);
    if (menu) {
        buildContextMenu(div);
    };
};

}, '' ,{requires:['node','yui2-container','yui2-menu','yui2-treeview','yui2-animation','yui2-dragdrop','yui2-json','yui2-connection']});
