{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{uniqueid prepend="slideshow" assign="name"}

{css unique="photoalbum`$name`" corecss="common,pagination" link="`$asset_path`css/yui3-slideshow.css"}

{/css}

<div class="module photoalbum slideshow">

    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create == 1}
            {icon class=add action=edit rank=1 title="Add a Slide"|gettext text="Add a Slide"|gettext}
        {/if}
        {if $permissions.edit == 1 && $slides|@count>1}
            {ddrerank items=$slides model="photo" label="Slides"}
        {/if}
    </div>
    {/permissions}

    <div id="ss-{$name}" class="slideshow-container" style="width:{$config.width|default:350}px;">    
        <ul class="slideshow-frame"{if $config.width} style="width:{$config.width}px;height:{$config.height}px;"{/if}>
            {foreach key=key from=$slides item=slide name=slides}
            <li class="slide" style="position:absolute;{if $smarty.foreach.slides.first}z-index:4;{else}z-index:1;{/if}">
                <div class="bodycopy">

                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit == 1}
                                {icon action=edit record=$slide title="Edit `$item->title`"}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon action=delete record=$slide title="Delete `$item->title`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                            {/if}
                            {if $permissions.create == 1}
                                {icon class=add action=edit rank=`$slide->rank+1` title="Add another slide here"  text="Add another slide here"}
                            {/if}
                        </div>
                    {/permissions}

                    {if !$config.hidetext}
                        <h2>{$slide->title}</h2>
                        {$slide->body}
                    {/if}
                </div>
                {if $config.quality==100}
                    <img src="{$slide->expFile[0]->url}" class="slide-image" />
                {else}
                    {img file_id=$slide->expFile[0]->id w=$config.width|default:350 h=$config.height|default:200 class="slide-image" zc=1 q=$config.quality|default:80}
                {/if}
            </li>
            {foreachelse}
                <li>{"No slides yet"|gettext}</li>
            {/foreach}
        </ul>

        <div class="slideshow-buttons">
            <a id="prev{$name}" href="javascript:void(0);" class="prev_slide" title="Prevous Slide">
                &lt;&lt; Previous
            </a>
            <span class="slideshow-pagination">
                {foreach key=key from=$slides item=slide name=slides}
                <a class="slide-page-link" href="#" rel="{$key}">
                    {$smarty.foreach.slides.iteration}
                </a>
                {/foreach}
            </span>
            <a id="plps{$name}" href="javascript:void(0);" class="pause_slide" title="Pause Slideshow">
                Pause
            </a>
            <a id="plps{$name}" href="javascript:void(0);" class="play_slide hide" title="Play Slideshow">
                Play
            </a>
            <a id="next{$name}" href="javascript:void(0);" class="next_slide" title="Next Slide">
                Next &gt;&gt;
            </a>
        </div>
    
    </div>
</div>

{if $slides|@count > 1}
{script unique="slideshow" yui3mods="anim"}
{literal}

YUI({   
base:EXPONENT.YUI3_PATH,
loadOptional: true,
modules: {
		'gallery-yui-slideshow': {
			fullpath: '{/literal}{$asset_path}js/yui3-slideshow.js{literal}',
			requires: ['anim'],
			optional: [],
			supersedes: []
		}
	}
}).use('gallery-yui-slideshow', function(Y) {
    var oSlideshow = new Y.Slideshow('#ss-{/literal}{$name}{literal} .slideshow-frame',
    {
        interval:{/literal}{$config.speed|default:5}000{literal},
        nextButton:"#ss-{/literal}{$name}{literal} .next_slide",
        previousButton:"#ss-{/literal}{$name}{literal} .prev_slide",
        playButton:"#ss-{/literal}{$name}{literal} .play_slide",
        pauseButton:"#ss-{/literal}{$name}{literal} .pause_slide",
        pagination:"#ss-{/literal}{$name}{literal} .slideshow-pagination a"
    });
});


{/literal}
{/script}
{/if}
