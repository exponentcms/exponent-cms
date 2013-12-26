{*
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
 *}

{css unique="slingbar" link="`$asset_path`css/slingbar.css" corecss="admin-global"}

{/css}

<div id="admintoolbar">

</div>

{*FIXME convert to yui3*}
{script unique="admin99" yui3mods="yui"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','dd','anim','event-custom','cookie','yui2-yahoo-dom-event','yui2-menu','yui2-connection','yui2-container', function(Y) {
         var YAHOO=Y.YUI2;
         var aItemData = [
            {/literal}{$menu}{literal},
         ];
         var oMenuBar = new YAHOO.widget.MenuBar("mymenubar", {
                                                     itemdata: aItemData
                                                     });
          oMenuBar.render("admintoolbar");
         // function onSubmenuShow() {
         //
         //        var oIFrame,
         //            oElement,
         //         nOffsetWidth;
         //     if ((this.id == "filemenu" || this.id == "editmenu") && YAHOO.env.ua.ie) {
         //
         //         oElement = this.element;
         //         nOffsetWidth = oElement.offsetWidth;
         //         oElement.style.width = nOffsetWidth + "px";
         //         oElement.style.width = (nOffsetWidth - (oElement.offsetWidth - nOffsetWidth)) + "px";
         //     }
         // }
         //oMenuBar.subscribe("show", onSubmenuShow);

         var tb = Y.one('#admintoolbar');

         //Selector of the node to make draggable
         var dd = new Y.DD.Drag({
             node: tb
         }).plug(Y.Plugin.DDConstrained, {
             stickY:true
         });

         //are we on the top or bottom?
         var top = {/literal}{$top}{literal};

         //set the slingbar to either the top or bottom
         if (top==1){
             tb.setStyle("top","0");
         }else {
             tb.setStyle("bottom","0");
         }
         tb.setStyle("display","block");

         dd.on("drag:end",function(e){
             //viewport height
             var scrollh = Y.DOM.docScrollY();
             //drop zone lattitude
             var h = Y.DOM.winHeight()+scrollh;
             //drop zone lattitude
             var dz = e.pageY;
             //toolbar height (counting shadow)
             var tbh = tb.getComputedStyle('height').replace('px','');
             //shadow height
             var sh = 0;
             //threshold - higher the number, the sooner the toolbar snaps to the other side
             var threshold = 25;
             //set up the animation
             var anim = new Y.Anim({
                 node: tb,
                 duration: 0.5,
                 easing: Y.Easing.elasticOut
             });

             var recordPosition = function() {
                 Y.Cookie.set("slingbar-top", top);
             };

             if (top==1) {
                 if ((h/threshold)<dz) {
                     top = 0;
                     anim.set('to', { xy: [0, h-tbh+sh] });
                     //recordPosition();
                 } else {
                     anim.set('to', { xy: [0, scrollh-sh] });
                 }
                 anim.run();

             } else {
                 if (((h/threshold)*(threshold-1))>dz) {
                     top = 1;
                     anim.set('to', { xy: [0, scrollh-sh] });
                     //recordPosition();
                 } else {
                     anim.set('to', { xy: [0, h-tbh+sh] });
                 }
                 anim.run();
             }

             anim.on("end",recordPosition);

         });

         var err = function () {
             alert("{/literal}{"Your popup blocker has prevented the file manager from opening"|gettext}{literal}");
         }

         var adminerwindow = function (){
             var win = window.open('{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/adminer/admin.php?server={/literal}{$smarty.const.DB_HOST}{literal}&username={/literal}{$smarty.const.DB_USER}{literal}&db={/literal}{$smarty.const.DB_NAME}{literal}');
             if (!win) { err(); }
         }

         var docswindow = function (){
             var win = window.open('http://docs.exponentcms.org');
             if (!win) { err(); }
         }

         var forumswindow = function (){
             var win = window.open('http://forums.exponentcms.org');
             if (!win) { err(); }
         }

         var reportbugwindow = function (){
             var win = window.open('http://exponentcms.lighthouseapp.com/projects/61783-exponent-cms/tickets/new');
             if (!win) { err(); }
         }

         var filepickerwindow = function (){
             var win = window.open('{/literal}{link controller=file action=picker ajax_action=1 update=noupdate}{literal}', 'IMAGE_BROWSER','left=0,top=0,scrollbars=yes,width={/literal}{$smarty.const.FM_WIDTH}{literal},height={/literal}{$smarty.const.FM_HEIGHT}{literal},toolbar=no,resizable=yes,status=0');
             if (!win) { err(); }
         }

         var fileuploaderwindow = function (){
             var win = window.open('{/literal}{link controller=file action=uploader ajax_action=1 update=noupdate}{literal}', 'IMAGE_BROWSER','left=0,top=0,scrollbars=yes,width={/literal}{$smarty.const.FM_WIDTH}{literal},height={/literal}{$smarty.const.FM_HEIGHT}{literal},toolbar=no,resizable=yes,status=0');
             if (!win) { err(); }
         }

         Y.on('toolbar:loaded',function(){
             if (document.getElementById("reportabug-toolbar")) Y.one('#reportabug-toolbar').on('click', reportbugwindow);
             if (document.getElementById("manage-db")) Y.one('#manage-db').on('click', adminerwindow);
             if (document.getElementById("docs-toolbar")) Y.one('#docs-toolbar').on('click',docswindow);
             if (document.getElementById("forums-toolbar")) Y.one('#forums-toolbar').on('click',forumswindow);
             if (document.getElementById("filemanager-toolbar")) Y.one('#filemanager-toolbar').on('click',filepickerwindow);
             if (document.getElementById("fileuploader-toolbar")) Y.one('#fileuploader-toolbar').on('click',fileuploaderwindow);
             // Y.later(900,this,function(){
             //     tb.setStyles({'opacity':'0.3'});
             // });
         });

        if (document.getElementById("ordernum")) Y.one('#ordernum').on('click',function(e){
            Y.later(100, e, function(e) {
                e.focus();
            }, e.currentTarget);
         });

         Y.fire('toolbar:loaded');

     });
{/literal}
{/script}

{if $smarty.const.LOGGER}
<div id="yuilogger" class="yui3-skin-sam">

</div>
{script unique="ylogger99" yui3mods="yui"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('console','console-filters','dd-plugin', function(Y) {
        var yconsole = new Y.Console({
            /* any other configuration */
            logSource: Y.Global,
            newestOnTop : false,
            style: 'separate',
            plugins: [
                Y.Plugin.ConsoleFilters,
                Y.Plugin.Drag, { handles: ['.yui3-console-hd'] }
            ]
        }).render("#yuilogger");
        yconsole.collapse();
    });
{/literal}
{/script}
{/if}