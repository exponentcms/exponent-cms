{css unique="slingbar" link="`$asset_path`css/slingbar.css" corecss="admin-global"}
    
{/css}


<div id="admintoolbar">
    
<table border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="tc">&nbsp;</td>
    </tr>
    <tr>
        <td class="mc">
            <div id="admintoolbarmenus">

            </div>
        </td>
    </tr>
    <tr>
        <td class="bc">&nbsp;</td>
    </tr>
</table>    

</div>

{script unique="adminmenubar" yui2mods="menu,connection" yui3mods="yui"}
{literal}
/*
              Initialize and render the MenuBar when the page's DOM is ready 
              to be scripted.
         */

         YAHOO.util.Event.onDOMReady(function () {
             var aItemData = [
                {/literal}{$menu}{literal},
             ];
             var oMenuBar = new YAHOO.widget.MenuBar("mymenubar", { 
                                                         lazyload: true, 
                                                         itemdata: aItemData 
                                                         });
              oMenuBar.render("admintoolbarmenus");
             function onSubmenuShow() {

                    var oIFrame,
                        oElement,
                     nOffsetWidth;
                 if ((this.id == "filemenu" || this.id == "editmenu") && YAHOO.env.ua.ie) {

                     oElement = this.element;
                     nOffsetWidth = oElement.offsetWidth;
                     oElement.style.width = nOffsetWidth + "px";
                     oElement.style.width = (nOffsetWidth - (oElement.offsetWidth - nOffsetWidth)) + "px";
                 }
             }
             //oMenuBar.subscribe("show", onSubmenuShow);
             
         
         });
         YUI(EXPONENT.YUI3_CONFIG).use('node','dd','anim', function(Y) {
             var tb = Y.get('#admintoolbar');

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
                 tb.setStyle("top","-5px");
             }else {
                 tb.setStyle("bottom","-5px");
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
                 var tbh = 36;
                 //shadow height 
                 var sh = 5;
                 //threshold - higher the number, the sooner the toolbar snaps to the other side
                 var threshold = 25;
                 //set up the animation                         
                 var anim = new Y.Anim({
                     node: tb,
                     duration: 0.5,
                     easing: Y.Easing.elasticOut
                 });

                 var recordPosition = function() {
                     var ajx = new EXPONENT.AjaxEvent();                     
                     ajx.fetch({action:"update_SetSlingbarPosition",controller:"administrationController",params:"&top="+top});
                 };

                 if (top==1) {
                     if ((h/threshold)<dz) {
                         top = 0;
                         anim.set('to', { xy: [0, h-tbh+sh] });
                         recordPosition();
                     } else {
                         anim.set('to', { xy: [0, scrollh-sh] });
                     }
                     anim.run();

                 } else {
                     if (((h/threshold)*(threshold-1))>dz) {
                         top = 1;
                         anim.set('to', { xy: [0, scrollh-sh] });
                         recordPosition();
                     } else {
                         anim.set('to', { xy: [0, h-tbh+sh] });
                     }
                     anim.run();
                 }
                 
                 
                 //anim.on("end",recordPosition);
                 
             });


         });
         

{/literal}
{/script}
