{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
{if $params.files[1]->id}
<div class="file thumbnails">
	{if $title}<h2>{$title}</h2>{/if}
	{foreach from=$files item=pic}
		{if $pic->is_image}
		    <a class="imagepopper" href="#" rel="popimg{$pic->id}">
            {img id="img`$pic->id`" class=$params.class file_id=$pic->id hs=`$config.thumbnail_square` ws=`$config.thumbnail_square` fltr=sep|`$config.popup_sepia_color`"}
            														
		    <span>{icon img=view.png}</span>
		    </a>
            {img id="popimg`$pic->id`" class="`$params.class`enlarge hide" file_id=$pic->id constraint=true h=`$config.popup_height` w=`$config.popup_height` fltr=sep|`$config.popup_sepia_color`}
		{/if}
	{/foreach}
</div>
{script unique="popme" yuimodules="container"}
{literal}
YAHOO.util.Event.onDOMReady(function(){
    var picpop = new YAHOO.widget.Panel("imgpop", { 
                                        fixedcenter:true,
                                        zindex:50,
                                        modal:false,
                                        underlay:"{/literal}{$config.popup_matte}{literal}",
                                        visible:false 
                                        } );

                                        picpop.setBody('tmp');
                                        picpop.render(document.body);
    var poppers = YAHOO.util.Dom.getElementsByClassName('imagepopper', 'a');
    var imgenlarge = [];//YAHOO.util.Dom.getElementsByClassName({/literal}'{$params.class}enlarge'{literal}, 'img');
    YAHOO.util.Dom.addClass(picpop.element, 'exp-image');
    
    YAHOO.util.Event.on(poppers, 'click', function(e,o){
        YAHOO.util.Event.stopEvent(e);
        var targ = YAHOO.util.Event.getTarget(e);
        if (!YAHOO.util.Dom.hasClass(targ, 'imagepopper')) {
            var bTarg = YAHOO.util.Dom.getAncestorByClassName(targ, 'imagepopper');
        } else {
            var bTarg = targ;
        }
        
        if (!imgenlarge[bTarg.rel]){
            imgenlarge[bTarg.rel] = YAHOO.util.Dom.get(bTarg.rel);
        }

        
         YAHOO.util.Dom.removeClass(imgenlarge[bTarg.rel], 'hide');
         this.setBody(imgenlarge[bTarg.rel]);
         this.cfg.setProperty("fixedcenter",true);
         //this.render();
         this.show();
    },picpop,true);
    
});
{/literal}
{/script}
{else}
    {if $params.files[0]->id}{img id="img`$params.files[0]->id`" class=$params.class file_id=$params.files[0]->id contraint=1 width=200 height=300}{/if}
{/if}


