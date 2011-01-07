{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div id="{$config.uniqueid}" class="files slideshow yui-sldshw-displayer" style="width:{$config.slideshow_width}px;height:{$config.slideshow_height}px;{$style}">
    {foreach key=key from=$params.files item=item name=slides}
    <div id="frame_{$config.uniqueid}_{$item->id}" class="yui-sldshw-frame{if $smarty.foreach.slides.first==true} yui-sldshw-active{/if}" style="width:{$config.slideshow_width}px;height:{$config.slideshow_height}px">
        {* In order to fill out the slide window, see if the 
            image is best resized by height or width *}
        
            {math assign=computedHeight equation=ceil(((imh*confw)/imw)) imh=$item->image_height confw=$config.slideshow_width imw=$item->image_width}
            {math assign=computedWidth equation=ceil(((imw*confh)/imh)) imw=$item->image_width confh=$config.slideshow_height imh=$item->image_height}
    
        {if $computedHeight<=$config.slideshow_height}
            {assign var=imgheight value=$config.slideshow_height}
            {assign var=imgwidth value=$computedWidth}
        {else}
            {assign var=imgwidth value=$computedHeight}
            {assign var=imgwidth value=$config.slideshow_width}
        {/if}

        {img alt=$item->alt file_id=$item->id width=$imgwidth height=$imgheight constraint=1}

    </div>
    {/foreach}
</div>

{script unique="linkslideshow`$config.uniqueid`" yuimodules="animation" src="`$smarty.const.JS_FULL`exp-slideshow.js"}
{literal}

YAHOO.util.Event.onDOMReady(function(){
    
    var slideshow{/literal}{$config.uniqueid}{literal} = {
        speed: {/literal}{$config.slideshow_framelength}{literal}000,
        displayer : {/literal}"{$config.uniqueid}"{literal},
        linkslides: {},
        timer: {},
        init: function(){
            this.linkslides = new YAHOO.myowndb.slideshow(this.displayer,{effect:  YAHOO.myowndb.slideshow.effects.{/literal}{$config.slideshow_anim}{literal}});
            this.timer = YAHOO.lang.later(this.speed, this.linkslides , this.linkslides.transition, null , this.speed );
        }
    };
    slideshow{/literal}{$config.uniqueid}{literal}.init();
});

{/literal}
{/script}
{else}
<div class="files slideshow yui-sldshw-displayer {if $params.class}slideshow-{$params.class}{/if}" style="width:{$params.width}px;height:{$params.height}px">
    {img class=$params.class file_id=$params.files[0]->id width=$params.width height=`$params.height+100` constraint=1}    
</div>
{/if} 

