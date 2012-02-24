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

{css unique="files-showcase" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}

{if $config.floatthumb!="No Float" && !$config.lightbox}
    {capture assign="imgflot"}
    float:{$config.floatthumb|lower};
    {/capture}

    {capture assign="spacing"}
    margin:{$config.spacing}px;
    {/capture}
{/if}

{assign var=quality value=$config.quality|default:$smarty.const.THUMB_QUALITY}
<div class="showcase">
    <div class="main-img">
        {if $config.pio && $params.is_listing}
            {assign var=miw value=$config.listingwidth}
        {else}
            {assign var=miw value=$config.piwidth}
        {/if}
        {img file_id=$files[0]->id w=$miw alt="`$files[0]->alt`" class="mainimg `$config.tclass`"}
    </div>
    {if ($config.pio && !$params.is_listing) || !$config.pio}
    <div class="thumb-imgs">
        {foreach from=$files item=img key=key}<a href="{$img->url}" rel="showcase-{$img->id}" title="{$img->title}" class="image-link" style="margin:{$config.spacing}px;" />{img file_id=$img->id w=$config.thumb h=$config.thumb zc=1 q=$quality|default:75 style="`$imgflot``$spacing`" alt="`$img->alt`" class="`$config.tclass`"}</a>{/foreach}
    </div>
    {/if}
</div>

{if ($config.pio && !$params.is_listing) || !$config.pio}
{script unique="showcase" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','event', function(Y) {
    var thumbs = Y.all('.thumb-imgs .image-link');
    
    thumbs.on('click',function(e){
        e.halt();
    });
    thumbs.on('{/literal}{if $config.hoverorclick==1}click{else}mouseenter{/if}{literal}',function(e){
        e.halt();
        var mainimg = e.currentTarget.ancestor('.showcase').one('.main-img img');
        var newid = e.currentTarget.getAttribute('rel').replace('showcase-','');
        mainimg.setAttribute('src',EXPONENT.URL_FULL+"thumb.php?id="+newid+"&w=500&q=75");
    });
    //Y.Lightbox.init();    
});
{/literal}
{/script}
{/if}