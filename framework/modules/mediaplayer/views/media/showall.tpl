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

{uniqueid assign=mediaplayer}

{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.css"}

{/css}
{css unique="mediaelement-skins" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mejs-skins.css"}

{/css}

<div class="module flowplayer mediaplayer showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
	{permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit rank=1 title="Add a Media piece at the Top"|gettext text="Add a Media piece"|gettext}
			{/if}
			{if $permissions.manage == 1}
				{ddrerank items=$page->records model="media" label="Media Pieces"|gettext}
			{/if}
		</div>	
	{/permissions}   
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {pagelinks paginate=$page top=1}
    {foreach from=$page->records item=media key=key}
        {$filetype=$media->expFile.media[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
        <div class="item">
            <h2>{$media->title}</h2>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {icon action=edit record=$media title="Edit"|gettext|cat:" "|cat:$media->title|cat:" "|cat:("media piece"|gettext)}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete record=$media title="Delete"|gettext|cat:" "|cat:$media->title|cat:" "|cat:("media piece"|gettext)}
                    {/if}
                </div>
            {/permissions}
            <div class="video media">
                {if $filetype == "mp3"}
                    <audio class="{$config.video_style}" id="{$media->expFile.media[0]->filename}" preload="none" controls="controls"
                        src="{$smarty.const.PATH_RELATIVE}{$media->expFile.media[0]->directory}{$media->expFile.media[0]->filename}" type="audio/mp3"{if $config.autoplay} autoplay="true" {/if}>
                    </audio>
                {elseif $filetype == "mp4" || $filetype == "webm" || $filetype == "ogv" || $filetype == "flv" || $filetype == "f4v" || $media->url != ""}
                    <video class="{$config.video_style}" width="{$media->width|default:$config.video_width}" height="{$media->height|default:$config.video_height}"
                        id="player{$media->expFile.media[0]->id}"
                        {if $config.autoplay}
                            autoplay="true"
                        {/if}
                        {if $media->expFile.splash[0]->id}
                            poster="{$smarty.const.PATH_RELATIVE}{$media->expFile.splash[0]->directory}{$media->expFile.splash[0]->filename}"
                        {/if}
                        controls="controls" preload="none">
                        {if $media->url == ""}
                            <source type="{$media->expFile.media[0]->mimetype}" src="{$smarty.const.PATH_RELATIVE}{$media->expFile.media[0]->directory}{$media->expFile.media[0]->filename}" />
                        {else}
                            <source type="video/youtube" src="{$media->url}" />
                        {/if}
                    </video>
                {/if}
            </div>
            <div class="bodycopy">
                {$media->body}
            </div>            
        </div>
		{permissions}
			<div class="module-actions">		
				{if $permissions.create == 1}
					{icon class=add action=edit rank=$media->rank+1 title="Add a Media piece Here"|gettext text="Add a Media piece"|gettext}
				{/if}
			</div>
		{/permissions}
		{clear}
    {/foreach}
    {pagelinks paginate=$page bottom=1}
</div>

{$control = ''}
{if $config.control_play}{$control = "`$control`'playpause',"}{/if}
{if $config.control_stop}{$control = "`$control`'stop',"}{/if}
{if $config.control_scrubber}{{$control = "`$control`'progress',"}}{/if}
{if $config.control_time}{{$control = "`$control`'duration',"}}{/if}
{if $config.control_volume}{$control = "`$control`'volume',"}{/if}
{if $config.control_fullscreen}{{$control = "`$control`'fullscreen'"}}{/if}

{script unique="mediaelement" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}
{literal}
    $('audio,video').mediaelementplayer({
        success: function(player, node) {
            $('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        },
        features: [{/literal}{$control}{literal}]
    });
{/literal}
{/script}
