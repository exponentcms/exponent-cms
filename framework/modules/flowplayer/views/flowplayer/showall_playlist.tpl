{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<script src="{$smarty.const.URL_FULL}external/flowplayer3/example/flowplayer-3.2.6.min.js"></script>
<div class="module flowplayer showall-playlist">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    <a id="playlist-player" href="{$page->records[0]->expFile.video[0]->url}" style="display:block;width:{$config.video_width}px;height:{$config.video_height}px;">
    </a>
    {permissions}
		<div class="module-actions">		
			{if $permissions.edit == 1}
				{ddrerank items=$page->records model="flowplayer" label="Videos"|gettext}
			{/if}
		</div>	
    {/permissions}  
    <ul>
		{permissions}
			<div class="item">
				<li>
					<div class="module-actions">		
						{if $permissions.edit == 1}
							{icon class="add" action=edit title="Add a Video at the Top"|gettext text="Add a Video"|gettext}{br}
						{/if}
					</div>	
				</li>
			</div>
		{/permissions}  
		{foreach name=items from=$page->records item=video}
			<div class="item">
				<li><a class="li-link" href="#" onclick="swapvideo('{$video->expFile.video[0]->url}')">{$video->title}</a>
					{permissions}
						<div class="item-actions">
							{if $permissions.edit == 1}
								{icon action=edit record=$video title="Edit `$video->title` video"}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$video title="delete `$video->title` video" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
							{/if}
						</div>
					{/permissions}
				</li>
			</div>
			{permissions}
				<div class="module-actions">		
					{if $permissions.create == 1}
						{icon class="add" action=edit rank=`$video->rank+1` title="Add a Video Here"|gettext text="Add a Video"|gettext}
					{/if}
				</div>
			{/permissions}
			{clear}
		{/foreach}
    </ul>
    <!-- this script block will install Flowplayer inside previous anchor tag --> 
    <script language="JavaScript"> 
        flowplayer("playlist-player", "{$smarty.const.PATH_RELATIVE}external/flowplayer3/flowplayer-3.2.7.swf",
            {literal}
            {
				wmode: 'opaque',
                clip:{ 
                    url: '{/literal}{$page->records[0]->expFile.video[0]->url}{literal}', 
					autoPlay:{/literal}{if $config.autoplay}true{else}false{/if}{literal},  
                    autoBuffering: false  
                }, 
                plugins:  { 
                    controls: { 
                        play: true,  
                        scrubber: true 
                    }         
                } 
            }
            {/literal}
        ); 
    </script>
</div>

{script unique="playlist"}
{literal}
    function swapvideo(url) {
        $f("playlist-player").stop();
        $f("playlist-player").play(url);
    }
{/literal}
{/script}
