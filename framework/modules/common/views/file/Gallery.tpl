{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}

{if $config.lightbox}
{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/gallery-lightbox.css"}

{/css}    
{/if}

{if $config.floatthumb!="No Float" && !$config.lightbox}
    {capture assign="imgflot"}
    float:{$config.floatthumb|lower};
    {/capture}

    {capture assign="spacing"}
    margin:{$config.spacing}px;
    {/capture}
{/if}

{*{assign var=quality value=$config.quality|default:$smarty.const.THUMB_QUALITY}*}
{$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}

{foreach from=$files item=img key=key}
	{if $config.lightbox}<a href="{$img->url}" rel="lightbox['{$config.uniqueid}']" title="{$img->title}" class="image-link" style="margin:{$config.spacing}px;{if $config.floatthumb!="No Float"}float:{$config.floatthumb|lower};{/if}">{/if}
		{if $key==0 && $config.piwidth}
			{img file_id=$img->id w=$config.piwidth|default:$config.thumb style="`$imgflot``$spacing`" alt="`$img->alt`" class="mainimg `$config.tclass`"}
		{else}
			{img file_id=$img->id w=$config.thumb h=$config.thumb zc=1 q=$quality|default:75 style="`$imgflot``$spacing`" alt="`$img->alt`" class="`$config.tclass`"}
		{/if}
	{if $config.lightbox}</a>{/if}
{/foreach}

{if $config.lightbox}
{script unique="shadowbox" yui3mods=1}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
    'gallery-lightbox' : {
       fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
       requires : ['base','node','anim','selector-css3']
    }
}

YUI(EXPONENT.YUI3_CONFIG).use('gallery-lightbox', function(Y) {
    Y.Lightbox.init();    
});
{/literal}
{/script}
{/if}