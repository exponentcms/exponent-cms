{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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
 
 {css unique="photo-album" link="`$smarty.const.PATH_RELATIVE`framework/modules/photoalbum/assets/css/photoalbum.css"}

 {/css}


<div class="module photoalbum show">
    <h1>{$record->title}</h1>

    {permissions}
    <div class="item-actions">
        {if $permissions.edit == 1}
            {icon img=edit.png action=edit id=$record->id title="Edit `$record->title`" text="Edit `$record->title`"}
        {/if}
    </div>
    {/permissions}
    
    {img alt=$record->alt file_id=$record->expFile[0]->id w=$config.pa_showall_enlarged zc=1 class="enlarged" title=$record->alt}

    {*if $record->expTag != ""}
        <div class="tag">
            Tags: 
            {foreach from=$record->expTag item=tag name=tags}
                <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>
                {if $smarty.foreach.tags.last != 1},{/if}
            {/foreach}
        </div>
    {/if*}
    
    <div class="bodycopy">
        {$record->body}
    </div>
    
    {*if $config.usescomments}
        {comments content_type="blog" content_id=$record->id title="Comments"}
    {/if*}
</div>
