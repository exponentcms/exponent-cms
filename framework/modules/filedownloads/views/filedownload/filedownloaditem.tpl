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

{$orderarray = explode(' ',$config.order)}
{$order = $orderarray[0]}
{if $order == 'created_at' }
    {$date = 'created_at'}
{elseif $order == 'edited_at'}
    {$date = 'edited_at'}
{else}
    {$date = 'publish_date'}
{/if}
<div class="item">
    {$filetype=$file->expFile.downloadable[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
    {if $file->expFile.preview[0] != "" && $config.show_icon}
        {img class="preview-img" file_id=$file->expFile.preview[0]->id square=150}
    {/if}
    {if $config.datetag}
        <p class="post-date">
            <span class="month">{$file->$date|format_date:"%b"}</span>
            <span class="day">{$file->$date|format_date:"%e"}</span>
            <span class="year">{$file->$date|format_date:"%Y"}</span>
        </p>
    {/if}
    {if $config.quick_download}
        <h3{if $config.usecategories} class="{$cat->color}"{/if}>
            {if $file->ext_file}
                <a class=downloadfile href="{$file->ext_file}" title="{'Download'|gettext}" target="_blank">{$file->title}</a>
            {else}
                {icon action=downloadfile fileid=$file->id filenum=0 text=$file->title title="{'Download'|gettext}"}
            {/if}
        </h3>
    {else}
        {if $file->title}<h3{if $config.usecategories} class="{$cat->color}"{/if}><a {if !$config.usebody}class="readmore"{/if} href="{link action=show title=$file->sef_url}" title="{$file->body|summarize:"html":"para"}">{$file->title}</a></h3>{/if}
    {/if}
    <div class="attribution">
        {if !$config.usecategories && $file->expCat[0]->title != ""}
            <div>
                <span class="label cat">{'From'|gettext}</span>
                <span class="value">"{$file->expCat[0]->title}"</span>
            </div>
        {/if}
        {if $config.show_info}
            {if !$config.datetag}
                <span class="label dated">{'Dated'|gettext}:</span>
                <span class="value">{$file->$date|format_date}</span>
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
            {if $permissions.edit || ($permissions.create && $file->poster == $user->id)}
                {if $myloc != $file->location_data}
                    {if $permissions.manage}
                        {icon action=merge id=$file->id title="Merge Aggregated Content"|gettext}
                    {else}
                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                    {/if}
                {/if}
                {icon action=edit record=$file title="Edit this file"|gettext}
            {/if}
            {if $permissions.delete || ($permissions.create && $file->poster == $user->id)}
                {icon action=delete record=$file title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
            {/if}
        </div>
    {/permissions}
    {if $config.usebody!=2}
        <div class="bodycopy">
            {if $config.usebody==1}
                {*<p>{$file->body|summarize:"html":"paralinks"}</p>*}
                <p>{$file->body|summarize:"html":"parahtml"}</p>
            {else}
                {$file->body}
            {/if}
        </div>
    {/if}
    {if $config.usebody==1 || $config.usebody==2}
        <a class="readmore" href="{link action=show title=$file->sef_url}">{'Read more'|gettext}</a>
        &#160;&#160;
    {/if}
    {if !$config.quick_download}
        {if $file->ext_file}
            <a class=downloadfile href="{$file->ext_file}" title="{'Download'|gettext}" target="_blank">{'Download'|gettext}</a>
        {else}
            {icon action=downloadfile fileid=$file->id filenum=0 text='Download'|gettext}
        {/if}
    {/if}
    {clear}
    {*{if $config.show_player && !$file->ext_file && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}*}
        {*<a href="{$file->expFile.downloadable[0]->url}" style="display:block;width:360px;height:{if $filetype == "mp3"}26{else}240{/if}px;" class="filedownload-media">*}
            {*{if $file->expFile.preview[0] != ""}*}
                {*{img class="preview-img" file_id=$file->expFile.preview[0]->id w=360 h=240 zc=1}*}
            {*{/if}*}
        {*</a>*}
    {*{/if}*}

    {if $config.show_player && !$file->ext_file}
        {if $filetype == "mp3"}
            <audio id="{$file->expFile.downloadable[0]->filename}" preload="none" controls="controls" src="{$smarty.const.PATH_RELATIVE}{$file->expFile.downloadable[0]->directory}{$file->expFile.downloadable[0]->filename}" type="audio/mp3">
            </audio>
        {elseif $filetype == "mp4" || $filetype == "m4v" || $filetype == "webm" || $filetype == "ogv" || $filetype == "flv" || $filetype == "f4v"}
            <video width="360" height="240" src="{$smarty.const.PATH_RELATIVE}{$file->expFile.downloadable[0]->directory}{$file->expFile.downloadable[0]->filename}" type="{$file->expFile.downloadable[0]->mimetype}"
            	id="player{$file->expFile.downloadable[0]->id}"
                {if $file->expFile.preview[0]->id}
                    poster="{$file->expFile.preview[0]->id}"
                {/if}
            	controls="controls" preload="none">
            </video>
        {/if}
    {/if}
    {if $config.enable_facebook_like}
        <div id="fb-root"></div>
        <div class="fb-like" data-href="{link action=show title=$file->sef_url}" data-send="false" data-width="{$config.width|default:'450'}" data-show-faces="{if $config.showfaces}true{else}false{/if}" data-font="{$config.font|default:''}"{if $config.color_scheme} data-colorscheme="{$config.color_scheme}"{/if}{if $config.verb} data-action="{$config.verb}"{/if}"></div>
        {script unique='facebook_src'}
        {literal}
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        {/literal}
        {/script}
    {/if}
    {if $config.enable_tweet}
        <a href="https://twitter.com/share" class="twitter-share-button" data-url="{link action=show title=$file->sef_url}" data-text="{$file->title}"{if $config.layout} data-count="{$config.layout}"{/if}{if $config.size} data-size="{$config.size}"{/if} data-lang="en">{'Tweet'|gettext}</a>
        {script unique='tweet_src'}
        {literal}
            !function(d,s,id){
                var js,fjs=d.getElementsByTagName(s)[0];
                if(!d.getElementById(id)){
                    js=d.createElement(s);
                    js.id=id;
                    js.src="https://platform.twitter.com/widgets.js";
                    fjs.parentNode.insertBefore(js,fjs);
                }
            }(document,"script","twitter-wjs");
        {/literal}
        {/script}
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
