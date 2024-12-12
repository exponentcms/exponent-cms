{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.min.css"}

{/css}

{if !empty($config.enable_facebook_like) || !empty($config.displayfbcomments)}
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId={$config.app_id}&autoLogAppEvents=1" nonce="9wKafjYh"></script>
{/if}

{if $config.enable_tweet}
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
{/if}

{$orderarray = explode(' ',$config.order)}
{$order = $orderarray[0]}
{if $order == 'created_at'}
    {$date = 'created_at'}
{elseif $order == 'edited_at'}
    {$date = 'edited_at'}
{else}
    {$date = 'publish_date'}
{/if}
<div class="module filedownload show">
	<div class="item">
        {if $record->expFile.preview[0] != ""}
            {img class="preview-img" file_id=$record->expFile.preview[0]->id square=150}
        {/if}
        {if $config.datetag}
            <p class="post-date">
                <span class="month">{$record->$date|format_date:"%b"}</span>
                <span class="day">{$record->$date|format_date:"%e"}</span>
                <span class="year">{$record->$date|format_date:"%Y"}</span>
            </p>
        {/if}
        {if $record->title}<{$config.item_level|default:'h2'}>{$record->title}</{$config.item_level|default:'h2'}>{/if}
        <div class="item-actions">
            {printer_friendly_link}{export_pdf_link prepend='&#160;&#160;|&#160;&#160;'|not_bs}{br}
            {subscribe_link}
        </div>
        {$myloc=serialize($__loc)}
        {permissions}
			<div class="item-actions">
				{if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                    {if $myloc != $record->location_data}
                        {if $permissions.manage}
                            {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
					{icon action=edit record=$record title="Edit this file"|gettext}
				{/if}
				{if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
					{icon action=delete record=$record title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
				{/if}
			</div>
        {/permissions}
        <div class="attribution">
            {if !$config.datetag}
                <span class="label dated">{'Dated'|gettext}:</span>
                <span class="value">{$record->$date|format_date}</span>,
            {/if}
            <span class="value">{$record->downloads}</span>
            <span class="label downloads"> {'Downloads'|gettext}</span>,
            {$prepend = '&#160;&#160;|&#160;&#160;'|not_bs}
            {comments_count record=$record show=1 prepend=$prepend}
            {tags_assigned record=$record prepend=','|cat:$prepend}
        </div>
        <div class="bodycopy">
            {$record->body}
        </div>
        {if $record->ext_file}
            <a class=downloadfile href="{$record->ext_file}" title="{'Download'|gettext}" target="_blank">{'Download'|gettext}</a>
        {else}
            {if count($record->expFile.downloadable) > 1}
                <h3 style="border-bottom:1px solid #777777;">{'Downloads'|gettext}</h3>
            {/if}
            <ul>
                {foreach from=$record->expFile.downloadable item=file key=filenum name=files}
                    <li style="margin-bottom: 10px;">
                        {$filetype=$file->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
                        {if $filenum == 0 && !empty($record->title)}
                            {$title = $record->title}
                        {elseif !empty($file->title)}
                            {$title = $file->title}
                        {else}
                            {$title = $file->filename}
                        {/if}
                        {icon action=downloadfile fileid=$record->id filenum=$filenum title='Download'|gettext text=$title}
                        <div class="attribution">
                            {if $record->expFile.downloadable[$filenum]->duration}
                                <span class="label size">{'Duration'}:</span>
                                <span class="value">{$record->expFile.downloadable[$filenum]->duration}</span>
                                &#160;|&#160;
                            {elseif $record->expFile.downloadable[$filenum]->filesize}
                                <span class="label size">{'File Size'}:</span>
                                <span class="value">{$record->expFile.downloadable[$filenum]->filesize|bytes}</span>
                            {/if}
                        </div>
                        {*{if $config.show_player && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}*}
                            {*<a href="{$record->expFile.downloadable[$filenum]->url}" style="display:block;width:360px;height:{if $filetype == "mp3"}26{else}240{/if}px;" class="filedownload-media">*}
                                {*{if $record->expFile.preview[0] != ""}*}
                                    {*{img class="preview-img" file_id=$record->expFile.preview[0]->id w=360 h=240 zc=1}*}
                                {*{/if}*}
                            {*</a>*}
                        {*{/if}*}

                        {if $config.show_player && !$record->ext_file}
                            {if $filetype == "mp3"}
                                <audio id="player{$record->expFile.downloadable[0]->id}" preload="none" controls="controls" src="{$smarty.const.PATH_RELATIVE}{$record->expFile.downloadable[0]->directory}{$record->expFile.downloadable[0]->filename}" type="audio/mp3">
                                </audio>
                            {elseif $filetype == "mp4" || $filetype == "m4v" || $filetype == "webm" || $filetype == "ogv" || $filetype == "flv" || $filetype == "f4v"}
                                <video width="360" height="240" src="{$smarty.const.PATH_RELATIVE}{$record->expFile.downloadable[0]->directory}{$record->expFile.downloadable[0]->filename}" type="{$record->expFile.downloadable[0]->mimetype}"
                                	id="player{$record->expFile.downloadable[0]->id}"
                                    {if $record->expFile.preview[0]->id}
                                    poster="{$record->expFile.preview[0]->id}"
                                    {/if}
                                	controls="controls" preload="none">
                                </video>
                            {/if}
                        {/if}

                    </li>
                {/foreach}
            </ul>
        {/if}
        {if $config.enable_facebook_like}
            <div class="fb-like" data-href="{link action=show title=$record->sef_url}" data-width="{$config.fblwidth}" data-layout="{$config.fblayout|default:'standard'}" data-action="{$config.fbverb|default:'like'}" data-size="{$config.fblsize|default:'small'}" data-share="true"></div>
        {/if}
        {if $config.enable_tweet}
            <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="{$item->title}" data-url="{link action=show title=$item->sef_url}"{if $config.twsize} data-size="{$config.twsize}"{/if} data-show-count="false">{'Tweet'|gettext}</a>
        {/if}
        {clear}
        {comments record=$record title="Comments"|gettext}
	</div>
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
            iconSprite: EXPONENT.PATH_RELATIVE+'external/mediaelement/build/mejs-controls.svg',
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
