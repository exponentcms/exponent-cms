{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Phillip Ball
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
<div class="files flowplayer video-playlist">
    {if $config.af_video_width != "" || $config.af_video_height != ""}    
        <a id="{$id}" href="{$params.files[0]->url}" style="display:block;width:{$config.af_video_width}px;height:{$config.af_video_height}px;">
    {else}
        <a id="{$id}" href="{$params.files[0]->url}" style="display:block;width:320px;height:234px;">
    {/if}
    </a>
    <ul>
    {foreach name=items from=$params.files item=video}
        <li>
            <a class="pl-link" href="#{$id}" onclick="EXPONENT.{uniqueid}swapvideo('{$video->url}')">{$video->filename}</a>
        </li>
    {/foreach}
    </ul>
</div>

{script unique=$id src="`$smarty.const.URL_FULL`external/flowplayer3/example/flowplayer-3.0.3.min.js"}
{literal}
    flowplayer("{/literal}{$id}{literal}", "{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/flowplayer3/flowplayer-3.0.3.swf",
        {
            clip:{ 
                url: '{/literal}{$params.files[0]->url}{literal}', 
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
    ); 
    EXPONENT.{/literal}{uniqueid}{literal}swapvideo = function(url) {
        $f("{/literal}{$id}{literal}").stop();
        $f("{/literal}{$id}{literal}").play(url);
    }
{/literal}
{/script}
