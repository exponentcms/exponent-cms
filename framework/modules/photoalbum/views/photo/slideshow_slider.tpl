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

{uniqueid prepend="slider" assign="name"}

{$sel_height = $config.sel_height}
{if !$config.sel_height}
    {$sel_height = round($config.height/($slides|count+1))}
{/if}

{css unique="photoalbum`$name`" link="`$asset_path`css/slider.css"}
{literal}
    .sliderlist {
        width : {/literal}{$config.sel_width|default:180}{literal}px;
    }
    .sliderlist div {
        width : {/literal}{($config.sel_width|default:180)-20}{literal}px;
    }
    .sliderlist li {
        height: {/literal}{$sel_height|default:55}{literal}px;
    }
    .sliderlist li.on div {
        width : {/literal}{($config.sel_width|default:180)-30}{literal}px;
    }
    .sliderbox {
        width  : {/literal}{$config.width|default:600}{literal}px;
        height : {/literal}{$config.height|default:375}{literal}px;
        margin-left: {/literal}{($config.sel_width|default:180)+15}{literal}px;
    }
    .sliderbox img {
        width  : {/literal}{$config.width|default:600}{literal}px;
        height : {/literal}{$config.height|default:375}{literal}px;
    }
{/literal}
{/css}

<div class="module photoalbum slider">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create == 1}
            {icon class=add action=edit rank=1 text="Add a Slide"|gettext}
        {/if}
        {if $permissions.manage == 1 && $slides|@count>1}
            {icon controller=expCat action=manage model='photo' text="Manage Categories"|gettext}
            {ddrerank items=$slides model="photo" label="Slides"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}

    {assign var=myloc value=serialize($__loc)}
    <div id="ss-{$name}" class="slider">

        <ul class="sliderlist dpd-slidetab tab-nav">
			{assign var=quality value=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {foreach key=key from=$slides item=slide name=slides}
                <li class="slider{if $smarty.foreach.slides.first} on{/if}">
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit == 1}
                                {if $myloc != $slide->location_data}
                                    {if $permissions.manage == 1}
                                        {icon img='arrow_merge.png' action=merge id=$slide->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon img="edit.png" action=edit record=$slide title="Edit"|gettext|cat:" `$slide->title`"}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon img="delete.png" action=delete record=$slide title="Delete"|gettext|cat:" `$slide->title`"}
                            {/if}
                        </div>
                    {/permissions}
                    <a title="{$slide->title}" href="{link action=show title=$slide->sef_url}">
                        {img file_id=$slide->expFile[0]->id  title="View"|gettext|cat:" `$slide->title`" w=$config.th_width|default:64 h=$config.th_height|default:40 class="slide-image" zc=1 q=$quality|default:75}
                    </a>
                    <div class="thumb-text">
                        {if !$config.hidetext}<strong>{$slide->title}</strong>{/if}
                        {if $slide->body}{$slide->body}{/if}
                    </div>
                </li>
            {foreachelse}
                <li>{"No slides yet"|gettext}</li>
            {/foreach}
        </ul>

        <div class="sliderbox dpd-slidecontent tab-content">
            {foreach key=key from=$slides item=slide name=slides}
                <div class="tab-pannel">
                    {if $slide->link}<a href="{$slide->link}">{/if}
                        <img src="{$slide->expFile[0]->url}" class="slide-image" width="{$config.width|default:600}px" height="{$config.height|default:375}px" />
                    {if $slide->link}</a>{/if}
                </div>
            {foreachelse}
                <li>{"No slides yet"|gettext}</li>
            {/foreach}
       	</div>

    </div>
</div>

{if $slides|@count > 1}
{script unique="ss-`$name`" yui3mods="node, anim"}
{literal}

EXPONENT.YUI3_CONFIG.modules = {
	'slide': {
		fullpath: '{/literal}{$asset_path}js/slide.js{literal}',
		requires: ['node','anim']
	}
}

YUI(EXPONENT.YUI3_CONFIG).use('slide',function(Y){
	new Y.Slide('ss-{/literal}{$name}{literal}',{
		autoSlide:true,
		eventype:'mouseover',
		effect:'{/literal}{$config.anim|default:fade}{literal}',
		selectedClass:'on',
		timeout:{/literal}{$config.speed|default:5}000{literal},
		speed:0.{/literal}{$config.interval|default:5}{literal},
        touchmove:true,
	});
});

{/literal}
{/script}
{/if}
