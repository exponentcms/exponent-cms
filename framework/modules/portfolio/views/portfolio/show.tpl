{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

<div class="module portfolio show">
    <a class="back" href="{backlink}">Back to Portfolio Listings</a>
    <h1>{$record->title}</h1>
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.edit == 1}
            {icon action=edit id=$record->id title="edit `$record->title`"}
        {/if}
    {/permissions}
    
    <div class="portfolio-img" style="width:{$config.enlargedsize+10}px;">
        {img id="main-img" class="main-image" alt=$record->expFile[0]->alt file_id=$record->expFile[0]->id w=$config.enlargedsize}
        {if $record->expFile[1]->id}
            {foreach from=$record->expFile item=img key=key name=thumbs}
                {if $smarty.foreach.thumbs.iteration%4==0}
                    {assign var="style" value="margin-right:0"}
                {else}
                    {assign var="style" value=""}
                {/if}
                {img id="thumb-`$img->id`" class="thumbnail" alt=$img->alt file_id=$img->id w=$config.detailthumbsize h=$config.detailthumbsize zc=1 style=$style}
            {/foreach}
        {/if}
    </div>

    {if $record->expTag}
        <div class="tag">
            Tags: 
            {foreach from=$record->expTag item=tag name=tags}
                <a href="{link action=showByTag tag=$tag->sef_url}">{$tag->title}</a>
                {if $smarty.foreach.tags.last != 1},{/if}
            {/foreach}
        </div>
    {/if}
    <div class="bodycopy">
        {$record->body}
    </div>
</div>

{script unique="swapthumb"}
{literal}

YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node', function(Y) {
    var thumbs = Y.all('.portfolio-img img.thumbnail');
    var mainimg = Y.get('#main-img');
    
    var swapimage = function(e){
        var tmbid = e.target.get('id').split('-')[1];
        mainimg.set('src',EXPONENT.URL_FULL+"thumb.php?id="+tmbid+"&w={/literal}{$config.enlargedsize}{literal}");
    };
    
    thumbs.on('click',swapimage);
    
});

{/literal}
{/script}


