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

{uniqueid assign=flowplayer}

{script unique="flowplayer" src="`$smarty.const.FLOWPLAYER_RELATIVE`flowplayer-`$smarty.const.FLOWPLAYER_MIN_VERSION`.min.js"}
{/script}

{script unique=$flowplayer}
{literal}
flowplayer("playlist-player", EXPONENT.FLOWPLAYER_RELATIVE+"flowplayer-"+EXPONENT.FLOWPLAYER_VERSION+".swf",
    {
		wmode: 'transparent',
		clip: {
            url: '{/literal}{$page->records[0]->expFile.video[0]->url}{literal}',
            autoPlay: {/literal}{if $config.autoplay}true{else}false{/if}{literal},
            autoBuffering: false
			},
        plugins:  {
            controls: {
                url: '{/literal}{if $config.video_style == 1}flowplayer.controls-air-{$smarty.const.FLOWPLAYER_CONTROLS_VERSION}.swf{elseif $config.video_style == 2}flowplayer.controls-tube-{$smarty.const.FLOWPLAYER_CONTROLS_VERSION}.swf{else}flowplayer.controls-{$smarty.const.FLOWPLAYER_CONTROLS_VERSION}.swf{/if}{literal}',
                play: {/literal}{if !$config.control_play}false{else}true{/if}{literal},
                stop: {/literal}{if $config.control_stop}true{else}false{/if}{literal},
                scrubber: {/literal}{if $config.control_scrubber}true{else}false{/if}{literal},
                time: {/literal}{if $config.control_time}true{else}false{/if}{literal},
                mute: {/literal}{if $config.control_mute}true{else}false{/if}{literal},
                volume: {/literal}{if $config.control_volume}true{else}false{/if}{literal},
                fullscreen: {/literal}{if $config.control_fullscreen}true{else}false{/if}{literal},
            }
        }
    }
);
{/literal}
{/script}

<div class="module flowplayer showall-playlist">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {permissions}
   		<div class="module-actions">
   			{if $permissions.manage == 1}
   				{ddrerank items=$page->records model="flowplayer" label="Videos"|gettext}
   			{/if}
   		</div>
   	{/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <a id="playlist-player" href="{$page->records[0]->expFile.video[0]->url}" style="display:block;width:{$config.video_width}px;height:{$config.video_height}px;">
        {if $page->records[0]->expFile.splash[0]->url}
            {img file_id=$page->records[0]->expFile.splash[0]->id w=$config.video_width h=$config.video_height zc=1}
        {/if}
    </a>
    <ul>
        {permissions}
            <div class="module-actions">
                {if $permissions.edit == 1}
                    {icon class=add action=edit rank=1 title="Add a Video at the Top"|gettext text="Add a Video"|gettext}
                {/if}
            </div>
        {/permissions}
		{foreach name=items from=$page->records item=video}
			<div class="item">
				<li><a class="li-link" href="#" title="{'Play this video'|gettext}" onclick="swapvideo('{$video->expFile.video[0]->url}')">{$video->title}</a>
					{permissions}
						<div class="item-actions">
							{if $permissions.edit == 1}
								{icon action=edit record=$video title="Edit `$video->title` video"}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$video title="delete `$video->title` video"}
							{/if}
						</div>
					{/permissions}
                    {if $video->body}
                        <div class="info">
                            {$video->body}
                        </div>
                    {/if}
				</li>
			</div>
			{permissions}
				<div class="module-actions">		
					{if $permissions.create == 1}
						{icon class=add action=edit rank=$video->rank+1 title="Add a Video Here"|gettext text="Add a Video"|gettext}
					{/if}
				</div>
			{/permissions}
			{clear}
		{/foreach}
    </ul>
</div>

{script unique="playlist"}
{literal}
    function swapvideo(url) {
        $f("playlist-player").stop();
        $f("playlist-player").play(url);
    }
{/literal}
{/script}
