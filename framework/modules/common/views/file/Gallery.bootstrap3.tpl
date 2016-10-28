{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
{script unique="shadowbox-`$__loc->src`" jquery='jquery.colorbox'}
{literal}
    $('a.image-link').colorbox({
        href: $(this).href,
        ref: $(this).rel,
        photo: true,
        maxWidth: "100%",
        close:'<i class="fa fa-close" aria-label="close modal"></i>',
        previous:'<i class="fa fa-chevron-left" aria-label="previous photo"></i>',
        next:'<i class="fa fa-chevron-right" aria-label="next photo"></i>',
    });
{/literal}
{/script}
{/if}