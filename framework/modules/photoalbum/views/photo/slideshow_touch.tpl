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

{css unique="slideshow-touch" link="`$asset_path`css/slideshow_touch.css"}

{/css}

<div class="module photoalbum slideshow">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class=add action=edit rank=1 text="Add a Slide"|gettext}
            {icon class=add action=multi_add title="Quickly Add Many Images"|gettext text="Add Multiple Images"|gettext}
        {/if}
        {if $permissions.manage}
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
    <div id="ss-{$name}" class="slideshow-container">
        <ul style="width:{$config.width|default:350}px;">
            <div class="slideshow-frame">
            {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {foreach key=key from=$slides item=slide name=slides}
                <li class="slide" style="position:absolute;{if $smarty.foreach.slides.first}z-index:4;{else}z-index:1;{/if}"{if !$config.hidetext} data-plugin-slide-caption='{$slide->title} {$slide->body}'{/if}>
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $slide->poster == $user->id)}
                                {if $myloc != $slide->location_data}
                                    {if $permissions.manage}
                                        {icon action=merge id=$slide->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit record=$slide title="Edit"|gettext|cat:" `$item->title`"}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $slide->poster == $user->id)}
                                {icon action=delete record=$slide title="Delete"|gettext|cat:" `$item->title`"}
                            {/if}
                            {if $permissions.create}
                                {icon class=add action=edit rank=$slide->rank+1 title="Add another slide here"|gettext  text="Add After"|gettext}
                            {/if}
                        </div>
                    {/permissions}
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
            </div>
        </ul>
        {*{if !$config.hidecontrols}*}
            {*<div class="slideshow-buttons{if $config.dimcontrols} buttons-dim{/if}">*}
                {*<a id="prev{$name}" href="javascript:void(0);" class="prev_slide" title="Previous Slide">*}
                    {*&lt;&lt; {'Previous'|gettext}*}
                {*</a>*}
                {*<span class="slideshow-pagination">*}
                    {*{foreach key=key from=$slides item=slide name=slides}*}
                        {*<a class="slide-page-link" href="#" rel="{$key}">{$smarty.foreach.slides.iteration}</a>*}
                    {*{/foreach}*}
                {*</span>*}
                {*<a id="plps{$name}" href="javascript:void(0);" class="pause_slide" title="Pause Slideshow"|gettext>*}
                    {*{'Pause'|gettext}*}
                {*</a>*}
                {*<a id="plps{$name}" href="javascript:void(0);" class="play_slide hide" title="Play Slideshow"|gettext>*}
                    {*{'Play'|gettext}*}
                {*</a>*}
                {*<a id="next{$name}" href="javascript:void(0);" class="next_slide" title="Next Slide"|gettext>*}
                    {*{'Next'|gettext} &gt;&gt;*}
                {*</a>*}
            {*</div>*}
        {*{/if}*}
    </div>
</div>

{if $slides|@count > 1}
{script unique="ssj-`$name`" jquery="jquery.excoloSlider"}
{literal}
    $(function () {
        $("#ss-{/literal}{$name}{literal} .slideshow-frame").excoloSlider({
            width : {/literal}{$config.width|default:350}{literal},
            height : {/literal}{$config.height|default:300}{literal},
            interval : {/literal}{$config.speed|default:5}000{literal},
            captionAutoHide : true,
            autoPlay : {/literal}{$config.autoplay|default:true}{literal},
            animationDuration : {/literal}{$config.duration|default:5}00{literal},
            prevButtonImage : EXPONENT.JQUERY_RELATIVE +"addons/images/prev.png",
            nextButtonImage : EXPONENT.JQUERY_RELATIVE +"addons/images/next.png",
            pagerImage : EXPONENT.JQUERY_RELATIVE +"addons/images/pagericon.png",
        });
    });
{/literal}
{/script}
{/if}
