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

{uniqueid assign="id"}

{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.css"}

{/css}

{$orderarray = explode(' ',$config.order)}
{$order = $orderarray[0]}
{if $order == 'created_at' }
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
        {if $record->title}<h2>{$record->title}</h2>{/if}
        {printer_friendly_link}{export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}{br}
        {subscribe_link}
        {$myloc=serialize($__loc)}
        {permissions}
			<div class="item-actions">
				{if $permissions.edit == 1}
                    {if $myloc != $record->location_data}
                        {if $permissions.manage == 1}
                            {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
					{icon action=edit record=$record title="Edit this file"|gettext}
				{/if}
				{if $permissions.delete == 1}
					{icon action=delete record=$record title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
				{/if}
			</div>
        {/permissions}
        <div class="attribution">
            {if !$config.datetag}
                <span class="label dated">{'Dated'|gettext}:</span>
                <span class="value">{$record->$date|format_date}</span>
                &#160;|&#160;
            {/if}
            <span class="label downloads"># {'Downloads'|gettext}:</span>
            <span class="value">{$record->downloads}</span>
            {comments_count record=$record show=1 prepend='&#160;&#160;|&#160;&#160;'}
            {tags_assigned record=$record prepend='&#160;&#160;|&#160;&#160;'}
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
                                <audio id="{$record->expFile.downloadable[0]->filename}" preload="none" controls="controls" src="{$smarty.const.PATH_RELATIVE}{$record->expFile.downloadable[0]->directory}{$record->expFile.downloadable[0]->filename}" type="audio/mp3">
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
            <div id="fb-root"></div>
            <div class="fb-like" data-href="{link action=show title=$record->sef_url}" data-send="false" data-width="{$config.width|default:'450'}" data-show-faces="{if $config.showfaces}true{else}false{/if}" data-font="{$config.font|default:''}"{if $config.color_scheme} data-colorscheme="{$config.color_scheme}"{/if}{if $config.verb} data-action="{$config.verb}"{/if}"></div>
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
            <a href="https://twitter.com/share" class="twitter-share-button" data-url="{link action=show title=$record->sef_url}" data-text="{$record->title}"{if $config.layout} data-count="{$config.layout}"{/if}{if $config.size} data-size="{$config.size}"{/if} data-lang="en">{'Tweet'|gettext}</a>
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
        {comments record=$record title="Comments"|gettext}
	</div>
</div>

{if $config.show_player}
    {script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}
    {/script}

    {script unique="filedownload-`$id`"}
        $('audio,video').mediaelementplayer({
        	success: function(player, node) {
        		$('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        	}
        });
    {/script}
{/if}
