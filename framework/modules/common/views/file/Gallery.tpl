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

{uniqueid prepend="gallery" assign="name"}

{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}

{*{if $config.lightbox}*}
{*{css unique="files-gallery" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/gallery-lightbox.css"}*}

{*{/css}    *}
{*{/if}*}

{if $config.floatthumb!="No Float" && $config.floatthumb!="Bottom"}
    {capture assign="imgflot"}
    float:{$config.floatthumb|lower};
    {/capture}
{/if}
{capture assign="spacing"}
margin:{$config.spacing}px;
{/capture}

{$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}

{foreach from=$files item=img key=key}
    {if $key == 1 && $config.floatthumb=="Bottom"}{clear}{/if}
    {if $img->alt != ""}
        {$alt = $img->alt}
    {elseif $img->title!=""}
        {$alt = $img->title}
    {else}
        {$alt = $img->filename}
    {/if}
    {if $img->title != ""}
        {$title = $img->title}
    {else}
        {$title = $img->filename}
    {/if}
    {if ($config.pio && $params.is_listing && $key==0) || !$params.is_listing || !$config.pio}
        {if $config.lightbox}<a href="{$img->url}" rel="lightbox['{$config.uniqueid}']" title="{$title}" class="image-link" style="margin:{$config.spacing}px;{if $config.floatthumb!="No Float"}float:{$config.floatthumb|lower};{/if}">{/if}
            {if $key==0 && $config.piwidth}
                {img file_id=$img->id w=$config.piwidth|default:$config.thumb style="`$imgflot``$spacing`" alt="`$alt`" title="`$alt`" class="mainimg `$config.tclass`" far=TL}
            {else}
                {img file_id=$img->id w=$config.thumb h=$config.thumb far=TL f=jpeg q=$quality|default:75 style="`$imgflot``$spacing`" alt="`$alt`" title="`$alt`" class="`$config.tclass`"}
            {/if}
        {if $config.lightbox}</a>{/if}
    {else}
        {if $config.lightbox}<a href="{$img->url}" rel="lightbox['{$config.uniqueid}']" title="{$title}" class="image-link" style="margin:{$config.spacing}px;{if $config.floatthumb!="No Float"}float:{$config.floatthumb|lower};{/if}">{/if}
            {img file_id=$img->id w=$config.thumb h=$config.thumb far=TL f=jpeg q=$quality|default:75 style="`$imgflot``$spacing`" alt="`$alt`" title="`$alt`" class="hide"}
        {if $config.lightbox}</a>{/if}
	{/if}
{/foreach}

{if $config.lightbox && !expJavascript::inAjaxAction()}
{script unique="shadowbox" yui3mods=1}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
    'gallery-lightbox' : {
       fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
       requires : ['base','node','anim','selector-css3','lightbox-css']
    },
    'lightbox-css': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/css/gallery-lightbox.css',
        type: 'css'
    }
}

YUI(EXPONENT.YUI3_CONFIG).use('gallery-lightbox', function(Y) {
    Y.Lightbox.init();    
});
{/literal}
{/script}
{/if}