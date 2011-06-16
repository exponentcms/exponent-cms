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
 
{uniqueid prepend="slideshow`$params.record->id`" assign="name"}

{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}

<ul id="ss-{$name}" class="slideshow-frame" style="width:{$config.width|default:350}px;height:{$config.height|default:250}px;">
	{assign var=quality value=$config.quality}
	{if !$quality}
		{assign var=quality value=THUMB_QUALITY}
	{/if}
    {foreach key=key from=$files item=slide name=slides}
    <li class="slide" style="position:absolute;{if $smarty.foreach.slides.first}z-index:4;{else}z-index:1;{/if}">
        {if $config.quality==100}
            <img src="{$slide->url}" class="slide-image" />
        {else}
            {img file_id=$slide->id w=$config.width|default:350 h=$config.height|default:200 class="slide-image" zc=1 q=$quality|default:75}
        {/if}
    </li>
    {/foreach}
</ul>

{if $files|@count > 1}
{script unique="slideshow`$name`" yui3mods="1"}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
    	'gallery-yui-slideshow': {
    		fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/photoalbum/assets/js/yui3-slideshow.js',
    		requires: ['anim','node'],
    	}
    }

YUI(EXPONENT.YUI3_CONFIG).use('gallery-yui-slideshow', function(Y) {
    var oSlideshow = new Y.Slideshow('#ss-{/literal}{$name}{literal}',
    {interval:{/literal}{$config.speed|default:5}000{literal}}
    );
});

{/literal}
{/script}
{/if}