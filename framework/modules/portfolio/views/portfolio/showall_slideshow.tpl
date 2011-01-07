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

{* get a unique name *}
{uniqueid prepend="paslide" assign="name"}

{css unique="photoalbum" link="`$smarty.const.PATH_RELATIVE`framework/modules/photoalbum/assets/css/slideshow.css"}
{literal}
    #bd .distinction,
    #bd .top {
        background:none;
    }
{/literal}
{/css}



<div class="module imagegallery slideshow">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
            <div class="moduleactions">
                {if $permissions.create == 1}
                    {icon class="add" action=edit rank=1 title="Add to the top" text="Add a new slide at the beginning"}
                {/if}
                {if $permissions.edit == 1 && $page->records|@count>1}
                    {ddrerank items=$page->records model="photo" label="Slides"}
                {/if}
            </div>
    {/permissions}

    
    <div id="{$name}" class="files slideshow yui-sldshw-displayer" style="width:{$config.pa_slideshow_width}px;height:{$config.pa_slideshow_height}px">
        {foreach key=key from=$page->records item=record name=slides}
        {if $config.onlyfeatured=="" || $record->featured==1}
        <div id="frame{$record->id}" class="yui-sldshw-frame{if $smarty.foreach.slides.first==true} yui-sldshw-active{/if}" style="width:{$config.pa_slideshow_width}px;height:{$config.pa_slideshow_height}px">
            <div class="pa-slide-image" style="width:{$config.pa_image_width}px;height:{$config.pa_image_height}px">
                {img alt=$record->alt file_id=$record->expFile[0]->id w=$config.pa_image_width h=$config.pa_image_height zc=1}
            </div>
            <div class="caption box_shadow">
                <h2>
                    <a href="{link action=show title=$record->sef_url}" title="{$record->title|escape:"htmlall"}" class="thumbnail">
                        {$record->title}
                    </a>
                </h2>
                {$record->body|summarize:"html":"para"}
            </div>
            {permissions level=$smarty.const.UILEVEL_NORMAL}
                <div class="itemactions">
                    {if $permissions.edit == 1}
                        {icon action=edit id=$record->id title="Edit `$record->title`"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete id=$record->id title="Delete `$record->title`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                    {/if}
                    {if $permissions.edit == 1}
                        {if $smarty.foreach.linkloop.first == 0}
                            {icon action=rerank img=up.png id=$record->id push=up}    
                        {/if}
                        {if $smarty.foreach.linkloop.last == 0}
                            {icon action=rerank img=down.png id=$record->id push=down}
                        {/if}
                    {/if}
                    {if $permissions.create == 1}
                        {icon class="add addhere" action=edit rank=`$text->rank+1` title="Add another slide here"  text="Add another slide here"}
                    {/if}
                </div>
            {/permissions}
        </div>
        {counter assign="trucount"}
        {/if}
        {/foreach}
    </div>
    
    {if $trucount>1 && $config.pa_show_controls}
    <div class="slideshow-buttons">
        <a id="prev{uniqueid}" href="#" class="prev_slide" title="Prevous Slide">
            &lt;&lt; Previous</a>
        <a id="plps{uniqueid}" href="#" class="pause_slide" title="Play/Pause Slideshow">
            Pause</a>
        <a id="next{uniqueid}" href="#" class="next_slide" title="Next Slide">
            Next &gt;&gt;
        </a>
    </div>
    {/if}
</div>

{if $trucount>1 && $config!=""}
{script unique="linkslideshow" yuimodules="animation" src="`$smarty.const.JS_FULL`exp-slideshow.js"}
{literal}

YAHOO.util.Event.onDOMReady(function(){
    
    var slideshow{/literal}{uniqueid}{literal} = {
        speed: {/literal}{$config.pa_slideshow_framelength}{literal}000,
        displayer : {/literal}"{$name}"{literal},
        uniqueid: '{/literal}{uniqueid}{literal}',
        linkslides: {},
        timer: {},
        init: function(){
            this.linkslides = new YAHOO.myowndb.slideshow(this.displayer,{effect:  YAHOO.myowndb.slideshow.effects.{/literal}{$config.pa_slideshow_anim}{literal}});
            this.timer = YAHOO.lang.later(this.speed, this.linkslides , this.linkslides.transition, null , this.speed );
            
            //clicking play/pause
            YAHOO.util.Event.on("plps"+this.uniqueid, 'click', this.playpause,this,true);

            YAHOO.util.Event.on('next'+this.uniqueid, 'click', function(e){
                YAHOO.util.Event.stopEvent(e);
                this.pause();
                this.linkslides.transition();
            },this,true);
            YAHOO.util.Event.on('prev'+this.uniqueid, 'click', function(e){
                YAHOO.util.Event.stopEvent(e);
                this.pause();
                this.linkslides.transition({ reverse : true });
            },this,true);

        },
        pause : function () {
            this.timer.cancel();
            YAHOO.util.Dom.replaceClass("plps"+this.uniqueid, 'pause_slide', 'play_slide');
            YAHOO.util.Dom.get("plps"+this.uniqueid).innerHTML="Play";
        },
        play : function () {
            this.timer = YAHOO.lang.later(8000, this.linkslides , this.linkslides.transition, null , 8000 );
            this.linkslides.transition();
            YAHOO.util.Dom.replaceClass("plps"+this.uniqueid, 'play_slide', 'pause_slide');
            YAHOO.util.Dom.get("plps"+this.uniqueid).innerHTML="Pause";
        },
        playpause : function(e,o){
            YAHOO.util.Event.stopEvent(e);
            if (YAHOO.util.Dom.hasClass("plps"+this.uniqueid,"pause_slide")) {
                this.pause();
            } else {
                this.play();
            }
        }
    };
    slideshow{/literal}{uniqueid}{literal}.init();
});
{/literal}
{/script}
{/if}