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

<script src="{$smarty.const.PATH_RELATIVE}external/flowplayer3/example/flowplayer-3.0.3.min.js"></script>
<div class="module flowplayer showall">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}

    {foreach from=$page->records item=video}
        <a href="{$video->expFile.video[0]->url}" style="display:block;width:{$video->width}px;height:{$video->height}px;" class="player">
            {if $video->expFile.splash[0]->url}
                {img file_id=$video->expFile.splash[0]->id width=$video->width height=$video->height}
            {/if}
        </a> 
        {permissions level=$smarty.const.UILEVEL_NORMAL}
            {if $permissions.edit == 1}
            {icon img=edit.png action=edit id=$video->id title="Edit this video" text="Edit Video Settings"}{br}
            {/if}
        {/permissions}
    {/foreach}
    
    <!-- this script block will install Flowplayer inside previous anchor tag --> 
    <script language="JavaScript"> 
        flowplayer("a.player", "{$smarty.const.PATH_RELATIVE}external/flowplayer3/flowplayer-3.0.3.swf",
            {literal}
            {
                clip:{ 
                    url: '{/literal}{$video->expFile.video[0]->url}{literal}', 
                    autoPlay: false,  
                    autoBuffering: false  
                }, 
                plugins:  { 
                    controls: { 
                        play: true,  
                        scrubber: false 
                    }         
                } 
            }
            {/literal}
        ); 
    </script>
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.edit == 1}
            {icon img=edit.png action=edit title="Add a video" text="Add a new video"}
      {/if}
    {/permissions}        
</div>

