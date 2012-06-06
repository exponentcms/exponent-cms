{*
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
 *}

<div class="navigationmodule yui-top-nav">
<div id="yuimenubar" class="yuimenubar yuimenubarnav">
	<div class="bd">
		<ul class="first-of-type">
		{assign var=startdepth value=0}
		{foreach name="children" key=key from=$sections item=section}
		{assign var=nextkey value=$key+1}
		{assign var=previouskey value=$key-1}

		{if $sections[$previouskey]->depth < $section->depth && $smarty.foreach.children.first!=true}

		<div id="childfly_{$key}_{$section->id}" class="yuimenu">
			<div class="bd">
				<ul>

		{/if}

        <li class="{if $section->depth == 0}yuimenubaritem first-of-type{else}yuimenuitem{/if}">
        <a class="{if $section->depth == 0}yuimenubaritemlabel{else}yuimenuitemlabel{/if}" href="{if $section->active == 1}{$section->link}{else}#{/if}" {if $section->new_window} target="_blank"{/if}>{$section->name}</a>
        {if $sections[$nextkey]->depth == $section->depth}</li>{/if}

		{if $sections[$nextkey]->depth < $section->depth}
			{if $smarty.foreach.children.last==true}
				{assign var=nextdepth value=$startdepth}
			{else}
				{assign var=nextdepth value=$sections[$nextkey]->depth}
			{/if}
			{math equation="x-y" x=$section->depth y=$nextdepth assign=looper}
			{section name="close" loop=$looper}
						</li>
					</ul>
				</div>	
			</div>	

			{/section}
				</li>
		{/if}

		{/foreach}
		</ul>
	</div>
</div>

{script yuimodules='"menu","animation"' unique="yuimenubar"}
{literal}
YAHOO.util.Event.onContentReady("yuimenubar", function () {
    var ua = YAHOO.env.ua,
        oAnim;  // Animation instance

    /*
         "beforeshow" event handler for each submenu of the MenuBar
         instance, used to setup certain style properties before
         the menu is animated.
    */

    function onSubmenuBeforeShow(p_sType, p_sArgs) {

        var oBody,
            oElement,
            oShadow,
            oUL;
    
        if (this.parent) {

            oElement = this.element;

            /*
                 Get a reference to the Menu's shadow element and 
                 set its "height" property to "0px" to syncronize 
                 it with the height of the Menu instance.
            */

            oShadow = oElement.lastChild;
            oShadow.style.height = "0px";

            /*
                Stop the Animation instance if it is currently 
                animating a Menu.
            */ 
        
            if (oAnim && oAnim.isAnimated()) {
            
                oAnim.stop();
                oAnim = null;
            
            }

            /*
                Set the body element's "overflow" property to 
                "hidden" to clip the display of its negatively 
                positioned <ul> element.
            */ 

            oBody = this.body;

            //  Check if the menu is a submenu of a submenu.

            if (this.parent && 
                !(this.parent instanceof YAHOO.widget.MenuBarItem)) {
            
                /*
                    There is a bug in gecko-based browsers where 
                    an element whose "position" property is set to 
                    "absolute" and "overflow" property is set to 
                    "hidden" will not render at the correct width when
                    its offsetParent's "position" property is also 
                    set to "absolute."  It is possible to work around 
                    this bug by specifying a value for the width 
                    property in addition to overflow.
                */

                if (ua.gecko) {
                
                    oBody.style.width = oBody.clientWidth + "px";
                
                }
                
                /*
                    Set a width on the submenu to prevent its 
                    width from growing when the animation 
                    is complete.
                */
                
                if (ua.ie == 7) {

                    oElement.style.width = oElement.clientWidth + "px";

                }
            
            }

            oBody.style.overflow = "hidden";

            /*
                Set the <ul> element's "marginTop" property 
                to a negative value so that the Menu's height
                collapses.
            */ 

            oUL = oBody.getElementsByTagName("ul")[0];

            oUL.style.marginTop = ("-" + oUL.offsetHeight + "px");
        
        }

    }

    /*
        "tween" event handler for the Anim instance, used to 
        syncronize the size and position of the Menu instance's 
        shadow and iframe shim (if it exists) with its 
        changing height.
    */

    function onTween(p_sType, p_aArgs, p_oShadow) {

        if (this.cfg.getProperty("iframe")) {
        
            this.syncIframe();
    
        }
    
        if (p_oShadow) {
    
            p_oShadow.style.height = this.element.offsetHeight + "px";
        
        }
    
    }

    /*
        "complete" event handler for the Anim instance, used to 
        remove style properties that were animated so that the 
        Menu instance can be displayed at its final height.
    */

    function onAnimationComplete(p_sType, p_aArgs, p_oShadow) {

        var oBody = this.body,
            oUL = oBody.getElementsByTagName("ul")[0];

        if (p_oShadow) {
        
            p_oShadow.style.height = this.element.offsetHeight + "px";
        
        }

        oUL.style.marginTop = "";
        oBody.style.overflow = "";
        
        //  Check if the menu is a submenu of a submenu.

        if (this.parent && 
            !(this.parent instanceof YAHOO.widget.MenuBarItem)) {

            // Clear widths set by the "beforeshow" event handler

            if (ua.gecko) {
            
                oBody.style.width = "";
            
            }
            
            if (ua.ie == 7) {

                this.element.style.width = "";

            }
        
        }
        
    }

    /*
         "show" event handler for each submenu of the MenuBar 
         instance - used to kick off the animation of the 
         <ul> element.
    */

    function onSubmenuShow(p_sType, p_sArgs) {

        var oElement,
            oShadow,
            oUL;
    
        if (this.parent) {

            oElement = this.element;
            oShadow = oElement.lastChild;
            oUL = this.body.getElementsByTagName("ul")[0];
        
            /*
                 Animate the <ul> element's "marginTop" style 
                 property to a value of 0.
            */

            oAnim = new YAHOO.util.Anim(oUL, 
                { marginTop: { to: 0 } },
                1, YAHOO.util.Easing.elasticOut);


            oAnim.onStart.subscribe(function () {

                oShadow.style.height = "100%";
            
            });

            oAnim.animate();
       
        }
    
    }

    /*
         Instantiate a MenuBar:  The first argument passed to the 
         constructor is the id of the element in the page 
         representing the MenuBar; the second is an object literal 
         of configuration properties.
    */

    var yuimenubar = new YAHOO.widget.MenuBar("yuimenubar", { 
                                                autosubmenudisplay: true, 
                                                hidedelay: 750, 
                                                lazyload: true });

    /*
         Subscribe to the "beforeShow" and "show" events for 
         each submenu of the MenuBar instance.
    */
    
    yuimenubar.subscribe("beforeShow", onSubmenuBeforeShow);
    yuimenubar.subscribe("show", onSubmenuShow);


	yuimenubar.render(); 
});

{/literal}
{/script}

</div>
