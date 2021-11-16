{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{uniqueid assign="id"}

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}
{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.min.css"}

{/css}

{$orderarray = explode(' ',$config.order)}
{$order = $orderarray[0]}
{if $order == 'created_at'}
    {$date = 'created_at'}
{elseif $order == 'edited_at'}
    {$date = 'edited_at'}
{else}
    {$date = 'publish_date'}
{/if}

<div class="module filedownload showall headlines">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='filedownload' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='filedownload' text="Manage Categories"|gettext}
                {/if}
                {*{if $rank == 1}*}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
                {/if}
           {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    {$myloc=serialize($__loc)}
    {$cat="bad"}
    {foreach from=$page->records item=item name=files}
        {if $smarty.foreach.files.iteration<=$config.headcount || !$config.headcount}
            {if $cat !== $item->expCat[0]->id && $config.usecategories}
                <a href="{link action=showall src=$page->src group=$item->expCat[0]->id}" title='View this group'|gettext><h2 class="category">{if $item->expCat[0]->title!= ""}{$item->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2></a>
            {/if}
            <div class="item">
                {$filetype=$item->expFile.downloadable[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
                {if $item->expFile.preview[0] != "" && $config.show_icon}
                    {img class="preview-img" file_id=$item->expFile.preview[0]->id square=150}
                {/if}
                {if $config.datetag}
                    <p class="post-date">
                        <span class="month">{$item->$date|format_date:"%b"}</span>
                        <span class="day">{$item->$date|format_date:"%e"}</span>
                        <span class="year">{$item->$date|format_date:"%Y"}</span>
                    </p>
                {/if}
                <span{if $config.usecategories} class="{$cat->color}"{/if}>
                    {if $config.quick_download}
                        {if $item->ext_file}
                            <a class=downloadfile href="{$item->ext_file}" title="{'Download'|gettext}" target="_blank"> </a>
                        {else}
                            {icon img="download.png" action=downloadfile fileid=$item->id filenum=0 title="{'Download'|gettext}"}
                        {/if}
                    {/if}
                    <a {if !$config.quick_download}class="readmore" {/if}href="{link action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                </span>
                <div class="attribution" style="margin-left: 20px;">
                    {if !$config.usecategories && $item->expCat[0]->title != ""}
                        <div>
                            <span class="label cat">{'From'|gettext}</span>
                            <span class="value">"{$item->expCat[0]->title}"</span>
                        </div>
                    {/if}
                    {if $config.show_info}
                        {if !$config.datetag}
                            <span class="label dated">{'Dated'|gettext}:</span>
                            <span class="value">{$item->$date|format_date}</span>
                            &#160;|&#160;
                        {/if}
                        {if $item->expFile.downloadable[0]->duration}
                            <span class="label size">{'Duration'}:</span>
                            <span class="value">{$item->expFile.downloadable[0]->duration}</span>
                        {else}
                            <span class="label size">{'File Size'}:</span>
                            <span class="value">{if !empty($item->expFile.downloadable[0]->filesize)}{$item->expFile.downloadable[0]->filesize|bytes}{else}{'Unknown'|gettext}{/if}</span>
                        {/if}
                        &#160;|&#160;
                        <span class="label downloads"># {'Downloads'|gettext}:</span>
                        <span class="value">{$item->downloads}</span>
                        {comments_count record=$item prepend='&#160;&#160;|&#160;&#160;'|not_bs}
                        {tags_assigned record=$item prepend='&#160;&#160;|&#160;&#160;'|not_bs}
                    {/if}
                </div>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item title="Edit this file"|gettext}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon action=delete record=$item title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
                        {/if}
                    </div>
                {/permissions}
                {clear}
                {if $config.show_player && !$item->ext_file && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}
                    <a href="{$item->expFile.downloadable[0]->url}" style="display:block;width:360px;height:{if $filetype == "mp3"}26{else}240{/if}px;" class="filedownload-media">
                        {if $item->expFile.preview[0] != ""}
                            {img class="preview-img" file_id=$item->expFile.preview[0]->id w=360 h=240 zc=1}
                        {/if}
                    </a>
                {/if}
                {clear}
                {permissions}
                    <div class="module-actions">
                        {if $permissions.create}
                            {icon class=add action=edit title="Add a File Here" text="Add a File"|gettext}
                        {/if}
                    </div>
                {/permissions}
                {clear}
            </div>
            {$cat=$item->expCat[0]->id}
        {/if}
    {/foreach}
    {if $page->total_records > $config.headcount}
        {pagelinks paginate=$page more=1 text="More Files..."|gettext}
    {/if}
</div>

{if $config.show_player}
    {script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}

    {/script}

    {script unique="filedownload-`$id`"}
    {literal}
        mejs.i18n.language('{/literal}{substr($smarty.const.LOCALE,0,2)}{literal}'); // Setting language
        $('audio,video').mediaelementplayer({
            // Do not forget to put a final slash (/)
            pluginPath: '../build/',
            iconSprite: '{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/mediaelement/build/mejs-controls.svg',
            // this will allow the CDN to use Flash without restrictions
            // (by default, this is set as `sameDomain`)
            shimScriptAccess: 'always',
            defaultAudioWidth: 340,
            success: function(player, node) {
            // $('#' + node.id + '-mode').html('mode: ' + player.rendererName);
            },
        });
    {/literal}
    {/script}
{/if}
