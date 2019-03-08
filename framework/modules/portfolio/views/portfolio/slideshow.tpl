{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

{css unique="portfolio`$name`" corecss="common,pagination"}

{/css}

<div class="module photoalbum portfolio slideshow">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 text="Add a Portfolio Piece"|gettext}
			{/if}
			{if $permissions.manage}
                {icon class="downloadfile" action=export_csv text="Export as CSV"|gettext}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                {/if}
                {*{if $slides|@count>1 && $rank == 1}*}
                {if $slides|@count>1 && $config.order == 'rank'}
                    {ddrerank items=$slides model="portfolio" label="Portfolio Pieces"|gettext}
                {/if}
            {/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <div id="ss-{$name}" class="slideshow-container" style="width:{$config.width|default:350}px;">
        <ul class="slideshow-frame"{if $config.width} style="width:{$config.width}px;height:{$config.height}px;"{/if}>
            {foreach key=key from=$slides item=slide name=slides}
            <li class="slide item" style="position:absolute;{if $smarty.foreach.slides.first}z-index:4;{else}z-index:1;{/if}">
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
                            {icon action=copy record=$slide title="Copy"|gettext|cat:" `$item->title`"}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $slide->poster == $user->id)}
                            {icon action=delete record=$slide title="Delete"|gettext|cat:" `$item->title`"}
                        {/if}
                        {if $permissions.create}
                            {icon class=add action=edit rank=$slide->rank+1 title="Add another slide here"|gettext  text="Add another slide here"|gettext}
                        {/if}
                    </div>
                {/permissions}
                {if !$config.hidetext}
                    <div class="bodycopy">
                        <{$config.item_level|default:'h2'}>
                            <a href="{link action="show" title=$slide->sef_url}" title="{$slide->body|summarize:"html":"para"}">
                                {$slide->title}
                            </a>
                        </{$config.item_level|default:'h2'}>
                        {$slide->body}
                    </div>
                {/if}
                {if $config.quality==100}
                    <img src="{$slide->expFile[0]->url}" class="slide-image" />
                {else}
                    {img file_id=$slide->expFile[0]->id w=$config.width|default:350 h=$config.height|default:200 class="slide-image" zc=1 q=$config.quality|default:80}
                {/if}
            </li>
            {foreachelse}
                <li>
                    {permissions}
                        {if $permissions.create}
                            {message class=notice text="No slides created yet"|gettext}
                        {/if}
                    {/permissions}
                </li>
            {/foreach}
        </ul>
        {if !$config.hidecontrols}
        <div class="slideshow-buttons{if $config.dimcontrols} buttons-dim{/if}">
            <a id="prev{$name}" href="javascript:void(0);" class="prev_slide" title="Previous Slide"|gettext>
                &lt;&lt; {'Previous'|gettext}
            </a>
            <span class="slideshow-pagination">
                {foreach key=key from=$slides item=slide name=slides}
                <a class="slide-page-link" href="#" rel="{$key}">
                    {$smarty.foreach.slides.iteration}
                </a>
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
{script unique="ss-`$name`" yui3mods="gallery-yui-slideshow"}
{literal}

EXPONENT.YUI3_CONFIG.modules = {
	'gallery-yui-slideshow': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/photoalbum/assets/js/yui3-slideshow.js',
        requires: ['anim','node','slideshow-css'],
	},
    'slideshow-css': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/photoalbum/assets/css/yui3-slideshow.css',
        type: 'css'
    }
}

YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var oSlideshow = new Y.Slideshow('#ss-{/literal}{$name}{literal} .slideshow-frame',
    {
        interval:{/literal}{$config.speed|default:5}000{literal},
//        autoplay:{/literal}{$config.autoplay|default:true}{literal},
        ti:'{/literal}{$config.anim_in|default:"fadeIn"}{literal}',
        to:'{/literal}{$config.anim_out|default:"fadeOut"}{literal}',
        duration:{/literal}{$config.duration|default:0.5}{literal},
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
