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

{uniqueid prepend="slideshow" assign="name"}

{css unique="photoalbum`$name`" corecss="common"}

{/css}
{*{css unique="photoalbum`$name`" corecss="common" link="`$asset_path`css/yui3-slideshow.css"}*}

{*{/css}*}

<div class="module photoalbum slideshow">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create == 1}
            {icon class=add action=edit rank=1 text="Add a Slide"|gettext}
            {icon class=add action=multi_add title="Quickly Add Many Images"|gettext text="Add Multiple Images"|gettext}
        {/if}
        {if $permissions.manage == 1}
            {if !$config.disabletags}
                {icon controller=expTag class="manage" action=manage_module model='photo' text="Manage Tags"|gettext}
            {/if}
            {if $config.usecategories}
                {icon controller=expCat action=manage model='photo' text="Manage Categories"|gettext}
            {/if}
            {if $slides|@count>1 && $config.order == 'rank'}
                {ddrerank items=$slides model="photo" label="Slides"|gettext}
            {/if}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <div id="ss-{$name}" class="slideshow-container" style="width:{$config.width|default:350}px;">
        <ul class="slideshow-frame" style="width:{$config.width|default:350}px;height:{$config.height|default:300}px;">
            {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {foreach key=key from=$slides item=slide name=slides}
                <li class="slide" style="position:absolute;{if $smarty.foreach.slides.first}z-index:4;{else}z-index:1;{/if}">
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit == 1}
                                {if $myloc != $slide->location_data}
                                    {if $permissions.manage == 1}
                                        {icon action=merge id=$slide->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit record=$slide title="Edit"|gettext|cat:" `$item->title`"}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon action=delete record=$slide title="Delete"|gettext|cat:" `$item->title`"}
                            {/if}
                            {if $permissions.create == 1}
                                {icon class=add action=edit rank=$slide->rank+1 title="Add another slide here"|gettext  text="Add After"|gettext}
                            {/if}
                        </div>
                    {/permissions}
                    {if !$config.hidetext}
                        {if !empty($slide->title)}
                            {$title = $slide->title}
                        {elseif !empty($slide->expFile[0]->title)}
                            {$title = $slide->expFile[0]->title}
                        {else}
                            {$title = ''}
                        {/if}
                        <div class="bodycopy">
                            <h2>{$title}</h2>
                            {$slide->body}
                        </div>
                    {/if}
                    {if $slide->link}
                        <a href="{$slide->link}">
                    {/if}
                    {if $config.quality==100}
                        <img src="{$slide->expFile[0]->url}" class="slide-image" />
                    {else}
                        {img file_id=$slide->expFile[0]->id w=$config.width|default:350 h=$config.height|default:300 class="slide-image" far=TL f=jpeg q=$quality|default:75}
                    {/if}
                    {if $slide->link}
                        </a>
                    {/if}
                </li>
            {foreachelse}
                <li>{"No slides yet"|gettext}</li>
            {/foreach}
        </ul>
        {if !$config.hidecontrols}
            <div class="slideshow-buttons">
                <a id="prev{$name}" href="javascript:void(0);" class="prev_slide" title="Previous Slide">
                    &lt;&lt; {'Previous'|gettext}
                </a>
                <span class="slideshow-pagination">
                    {foreach key=key from=$slides item=slide name=slides}
                        <a class="slide-page-link" href="#" rel="{$key}">{$smarty.foreach.slides.iteration}</a>
                    {/foreach}
                </span>
                <a id="plps{$name}" href="javascript:void(0);" class="pause_slide" title="Pause Slideshow"|gettext>
                    {'Pause'|gettext}
                </a>
                <a id="plps{$name}" href="javascript:void(0);" class="play_slide hide" title="Play Slideshow"|gettext>
                    {'Play'|gettext}
                </a>
                <a id="next{$name}" href="javascript:void(0);" class="next_slide" title="Next Slide"|gettext>
                    {'Next'|gettext} &gt;&gt;
                </a>
            </div>
        {/if}
    </div>
</div>

{if $slides|@count > 1}
{script unique="ss-`$name`" yui3mods="anim"}
{literal}

EXPONENT.YUI3_CONFIG.modules = {
	'gallery-yui-slideshow': {
		fullpath: '{/literal}{$asset_path}js/yui3-slideshow.js{literal}',
        requires: ['anim','node','slideshow-css'],
    },
    'slideshow-css': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/photoalbum/assets/css/yui3-slideshow.css',
        type: 'css'
	}
}

YUI(EXPONENT.YUI3_CONFIG).use('gallery-yui-slideshow', function(Y) {
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
