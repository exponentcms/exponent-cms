{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<script src="{$smarty.const.URL_FULL}external/flowplayer3/example/flowplayer-3.0.3.min.js"></script>
<div class="module flowplayer showall">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    
    <a id="playlist-player" href="{$page->records[0]->expFile.video[0]->url}" style="display:block;width:{$config.video_width}px;height:{$config.video_height}px;">
    </a>
    <ul>
    {foreach name=items from=$page->records item=video}
        <li><a class="li-link" href="#" onclick="swapvideo('{$video->expFile.video[0]->url}')">{$video->title}</a>
            {permissions}
                <div class="actions">
                {if $permissions.edit == 1}
                    {icon class="edit" action=edit id=$video->id title="Edit `$video->title`"}
                {/if}
                {if $permissions.delete == 1}
                    {icon class="delete" action=delete id=$video->id title="delete `$video->title`"}
                {/if}
                {if $permissions.edit == 1}
                    {if $smarty.foreach.items.first == 0}
                        {icon controller=flowplayer action=rerank img=up.png id=$video->id push=up}    
                    {/if}
                    {if $smarty.foreach.items.last == 0}
                        {icon controller=flowplayer action=rerank img=down.png id=$video->id push=down}
                    {/if}
                {/if}
                </div>
            {/permissions}
        </li>
    {/foreach}
    </ul>
    <!-- this script block will install Flowplayer inside previous anchor tag --> 
    <script language="JavaScript"> 
        flowplayer("playlist-player", "{$smarty.const.PATH_RELATIVE}external/flowplayer3/flowplayer-3.0.3.swf",
            {literal}
            {
                clip:{ 
                    url: '{/literal}{$page->records[0]->expFile.video[0]->url}{literal}', 
                    autoPlay: false,  
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
    {permissions}
        {if $permissions.edit == 1}
            {icon class="add" action=edit title="Add a video" text="Add a new video"}
      {/if}
    {/permissions}        
</div>

{script unique="playlist"}
{literal}
    function swapvideo(url) {
        $f("playlist-player").stop();
        $f("playlist-player").play(url);
    }
{/literal}
{/script}
