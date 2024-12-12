{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{css unique="files-showcase" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}

{capture assign="spacing"}
margin:{$config.spacing}px;
{/capture}

{$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}

<div class="showcase">
    <div class="main-img">
        {if $config.pio && $params.is_listing}
            {$miw=$config.listingwidth|default:$config.piwidth}
        {else}
            {$miw=$config.piwidth|default:$config.listingwidth}
        {/if}
        {if $files[0]->alt != ""}
            {$alt = $files[0]->alt}
        {elseif $files[0]->title!=""}
            {$alt = $files[0]->title}
        {else}
            {$alt = $files[0]->filename}
        {/if}
        {img file_id=$files[0]->id w=$miw h=$miw alt="`$alt`" class="mainimg `$config.tclass`" far=TL title=$alt}
    </div>
    {if ($config.pio && !$params.is_listing) || !$config.pio}
        <div class="thumb-imgs">
            {foreach from=$files item=img key=key}
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
                <a href="{$img->url}" rel="showcase-{$img->id}" title="{$title}" class="image-link" style="margin:{$config.spacing}px;">{img file_id=$img->id w=$config.thumb h=$config.thumb far=TL f=jpeg q=$quality|default:75 style="`$spacing`" alt="`$alt`" class="`$config.tclass`"}</a>
            {/foreach}
        </div>
    {/if}
</div>

{if ($config.pio && !$params.is_listing) || !$config.pio}
{script unique="showcase" jquery=1}
{literal}
$(document).ready(function() {
    var thumbs = $('.thumb-imgs .image-link');

    thumbs.on('click',function(e){
        e.preventDefault();
    });
    thumbs.on('{/literal}{if $config.hoverorclick==1}click{else}mouseenter{/if}{literal}',function(e){
        e.preventDefault();
        var targ = $(e.target);
        var mainimg = targ.closest('.showcase').find('.main-img img');
        var newid = targ.parent('a').attr('rel');
        if (newid == null)  // hit on link instead of image?
            newid = targ.attr('rel');
        newid = newid.replace('showcase-', '');
        var newtitle = targ.parent().attr('title');
        if (newtitle == null)  // hit on link instead of image?
            newtitle = targ.attr('title');
        mainimg.attr('src', EXPONENT.PATH_RELATIVE + "thumb.php?id=" + newid + "&w={/literal}{$miw}{literal}&h={/literal}{$miw}{literal}&far=TL&f=jpeg&q={/literal}{$quality|default:75}{literal}");
        mainimg.attr('title', newtitle);
    });
});
{/literal}
{/script}
{/if}