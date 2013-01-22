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

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module filedownload showall headlines">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</h2>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage == 1}
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
    {foreach from=$page->records item=file name=files}
        {if $smarty.foreach.files.iteration<=$config.headcount || !$config.headcount}
            {if $cat !== $file->expCat[0]->id && $config.usecategories}
                <a href="{link action=showall src=$page->src group=$file->expCat[0]->id}" title='View this group'|gettext><h2 class="category">{if $file->expCat[0]->title!= ""}{$file->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2></a>
            {/if}
            <div class="item">
                {$filetype=$file->expFile.downloadable[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
                {if $file->expFile.preview[0] != "" && $config.show_icon}
                    {img class="preview-img" file_id=$file->expFile.preview[0]->id square=150}
                {/if}
                {if $config.datetag}
                    <p class="post-date">
                        <span class="month">{$file->publish_date|format_date:"%b"}</span>
                        <span class="day">{$file->publish_date|format_date:"%e"}</span>
                        <span class="year">{$file->publish_date|format_date:"%Y"}</span>
                    </p>
                {/if}
                <span{if $config.usecategories} class="{$cat->color}"{/if}>
                    {if $config.quick_download}
                        {if $file->ext_file}
                            <a class=downloadfile href="{$file->ext_file}" title="{'Download'|gettext}" target="_blank"> </a>
                        {else}
                            {icon img="download.png" action=downloadfile fileid=$file->id filenum=0 title="{'Download'|gettext}"}
                        {/if}
                    {/if}
                    <a {if !$config.quick_download}class="readmore" {/if}href="{link action=show title=$file->sef_url}" title="{$file->body|summarize:"html":"para"}">{$file->title}</a>
                </span>
                <div class="attribution" style="margin-left: 20px;">
                    {if !$config.usecategories && $file->expCat[0]->title != ""}
                        <div>
                            <span class="label cat">{'From'|gettext}</span>
                            <span class="value">"{$file->expCat[0]->title}"</span>
                        </div>
                    {/if}
                    {if $config.show_info}
                        {if !$config.datetag}
                            <span class="label dated">{'Dated'|gettext}:</span>
                            <span class="value">{$file->publish_date|format_date}</span>
                            &#160;|&#160;
                        {/if}
                        {if $file->expFile.downloadable[0]->duration}
                            <span class="label size">{'Duration'}:</span>
                            <span class="value">{$file->expFile.downloadable[0]->duration}</span>
                        {else}
                            <span class="label size">{'File Size'}:</span>
                            <span class="value">{if !empty($file->expFile.downloadable[0]->filesize)}{$file->expFile.downloadable[0]->filesize|bytes}{else}{'Unknown'|gettext}{/if}</span>
                        {/if}
                        &#160;|&#160;
                        <span class="label downloads"># {'Downloads'|gettext}:</span>
                        <span class="value">{$file->downloads}</span>
                        {comments_count record=$file prepend='&#160;&#160;|&#160;&#160;'}
                        {tags_assigned record=$file prepend='&#160;&#160;|&#160;&#160;'}
                    {/if}
                </div>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit == 1}
                            {if $myloc != $file->location_data}
                                {if $permissions.manage == 1}
                                    {icon action=merge id=$file->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$file title="Edit this file"|gettext}
                        {/if}
                        {if $permissions.delete == 1}
                            {icon action=delete record=$file title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
                        {/if}
                    </div>
                {/permissions}
                {clear}
                {if $config.show_player && !$file->ext_file && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}
                    <a href="{$file->expFile.downloadable[0]->url}" style="display:block;width:360px;height:{if $filetype == "mp3"}26{else}240{/if}px;" class="filedownload-media">
                        {if $file->expFile.preview[0] != ""}
                            {img class="preview-img" file_id=$file->expFile.preview[0]->id w=360 h=240 zc=1}
                        {/if}
                    </a>
                {/if}
                {clear}
                {permissions}
                    <div class="module-actions">
                        {if $permissions.create == 1}
                            {icon class=add action=edit title="Add a File Here" text="Add a File"|gettext}
                        {/if}
                    </div>
                {/permissions}
                {clear}
            </div>
            {$cat=$file->expCat[0]->id}
        {/if}
    {/foreach}
    {if $page->total_records > $config.headcount}
        {pagelinks paginate=$page more=1 text="More Files..."|gettext}
    {/if}
</div>

{if $config.show_player}
    {script unique="flowplayersrc" src="`$smarty.const.FLOWPLAYER_RELATIVE`flowplayer-`$smarty.const.FLOWPLAYER_MIN_VERSION`.min.js"}
    {/script}

    {script unique="flowplayerrun"}
    {literal}
    flowplayer("a.filedownload-media", EXPONENT.FLOWPLAYER_RELATIVE+"flowplayer-"+EXPONENT.FLOWPLAYER_VERSION+".swf",
        {
    		wmode: 'transparent',
    		clip: {
    			autoPlay: false,
    			},
            plugins:  {
                controls: {
                    play: true,
                    scrubber: true,
                    fullscreen: false,
                    autoHide: false
                }
            }
        }
    );
    {/literal}
    {/script}
{/if}
