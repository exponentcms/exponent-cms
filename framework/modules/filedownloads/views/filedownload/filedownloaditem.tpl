{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{$orderarray = explode(' ',$config.order)}
{$order = $orderarray[0]}
{if $order == 'created_at'}
    {$date = 'created_at'}
{elseif $order == 'edited_at'}
    {$date = 'edited_at'}
{else}
    {$date = 'publish_date'}
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
    {if $config.quick_download}
        <h3{if $config.usecategories} class="{$cat->color}"{/if}>
            {if $item->ext_file}
                <a class=downloadfile href="{$item->ext_file}" title="{'Download'|gettext}" target="_blank">{$item->title}</a>
            {else}
                {*{icon action=downloadfile fileid=$item->id filenum=0 text=$item->title title="{'Download'|gettext}"}*}
                <a class="downloadfile {button_style}" href="{link action=downloadfile fileid=$item->id filenum=0}" title="{'Download'|gettext}">{$item->title}</a>
            {/if}
        </h3>
    {else}
        {if $item->title}<h3{if $config.usecategories} class="{$cat->color}"{/if}><a {if !$config.usebody}class="readmore"{/if} href="{link action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></h3>{/if}
    {/if}
    <div class="attribution">
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
            {/if}
            {if $item->expFile.downloadable[0]->duration}
                <span class="label size">{'Duration'}:</span>
                <span class="value">{$item->expFile.downloadable[0]->duration}</span>,
            {else}
                <span class="label size">{'File Size'}:</span>
                <span class="value">{if !empty($item->expFile.downloadable[0]->filesize)}{$item->expFile.downloadable[0]->filesize|bytes}{else}{'Unknown'|gettext}{/if}</span>,
            {/if}
            <span class="value">{$item->downloads}</span>
            <span class="label downloads"> {'Downloads'|gettext}</span>,
            {$prepend = '&#160;&#160;|&#160;&#160;'|not_bs}
            {comments_count record=$item prepend=$prepend}
            {tags_assigned record=$item prepend=','|cat:$prepend}
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
    {$link = '<a href="'|cat:makeLink([controller=>filedownload, action=>show, title=>$item->sef_url])|cat:'"><em>'|cat:'(read more)'|gettext|cat:'</em></a>'}
    {if $config.usebody!=2}
        <div class="bodycopy">
            {if $config.usebody==1}
                {*<p>{$item->body|summarize:"html":"paralinks"}</p>*}
                <p>{$item->body|summarize:"html":"parahtml":$link}</p>
            {elseif $config.usebody==3}
                {$item->body|summarize:"html":"parapaged":$link}
            {else}
                {$item->body}
            {/if}
        </div>
    {elseif $config.quick_download}
        {$link}&#160;&#160;
    {/if}
    {*{if $config.usebody==1 || $config.usebody==2}*}
        {*<a class="readmore" href="{link action=show title=$item->sef_url}">{'Read more'|gettext}</a>*}
        {*&#160;&#160;*}
    {*{/if}*}
    {if !$config.quick_download}
        <div class="item-actions">
            {if $item->ext_file}
                <a class=downloadfile href="{$item->ext_file}" title="{'Download'|gettext}" target="_blank">{'Download'|gettext}</a>
            {else}
                {icon action=downloadfile fileid=$item->id filenum=0 text='Download'|gettext}
            {/if}
        </div>
    {/if}
    {clear}
    {*{if $config.show_player && !$item->ext_file && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}*}
        {*<a href="{$item->expFile.downloadable[0]->url}" style="display:block;width:360px;height:{if $filetype == "mp3"}26{else}240{/if}px;" class="filedownload-media">*}
            {*{if $item->expFile.preview[0] != ""}*}
                {*{img class="preview-img" file_id=$item->expFile.preview[0]->id w=360 h=240 zc=1}*}
            {*{/if}*}
        {*</a>*}
    {*{/if}*}

    {if $config.show_player && !$item->ext_file}
        {if $filetype == "mp3"}
            <audio id="player{$item->expFile.downloadable[0]->id}" preload="none" controls="controls" src="{$smarty.const.PATH_RELATIVE}{$item->expFile.downloadable[0]->directory}{$item->expFile.downloadable[0]->filename}" type="audio/mp3">
            </audio>
        {elseif $filetype == "mp4" || $filetype == "m4v" || $filetype == "webm" || $filetype == "ogv" || $filetype == "flv" || $filetype == "f4v"}
            <video width="360" height="240" src="{$smarty.const.PATH_RELATIVE}{$item->expFile.downloadable[0]->directory}{$item->expFile.downloadable[0]->filename}" type="{$item->expFile.downloadable[0]->mimetype}"
            	id="player{$item->expFile.downloadable[0]->id}"
                {if $item->expFile.preview[0]->id}
                    poster="{$item->expFile.preview[0]->id}"
                {/if}
            	controls="controls" preload="none">
            </video>
        {/if}
    {/if}
    {if $config.enable_facebook_like}
        <div class="fb-like" data-href="{link action=show title=$record->sef_url}" data-width="{$config.fblwidth}" data-layout="{$config.fblayout|default:'standard'}" data-action="{$config.fbverb|default:'like'}" data-size="{$config.fblsize|default:'small'}" data-share="true"></div>
    {/if}
    {if $config.enable_tweet}
        <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="{$item->title}" data-url="{link action=show title=$item->sef_url}"{if $config.twsize} data-size="{$config.twsize}"{/if} data-show-count="false">{'Tweet'|gettext}</a>
    {/if}
    {clear}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class=add action=edit title="Add a File Here"|gettext text="Add a File"|gettext}
            {/if}
        </div>
    {/permissions}
    {clear}
</div>
