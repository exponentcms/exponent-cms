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

<div class="module blog show">
    <h2>{$record->title}</h2>
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        <div class="itemactions">
            {if $permissions.edit == 1}
                {icon action=edit id=$record->id title="Edit this `$modelname`"}
            {/if}
            {if $permissions.delete == 1}
                {icon action=delete id=$record->id title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
            {/if}
        </div>
    {/permissions}
    <span class="post-info">Posted by {attribution user_id=$record->poster} on <span class="date">{$record->created_at|format_date:$smarty.const.DISPLAY_DATE_FORMAT}</span>
        <span class="tags">
            Tags: 
            {foreach from=$record->expTag item=tag name=tags}
            <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>
            {if $smarty.foreach.tags.last != 1},{/if}
            {/foreach} 
        </span>
    </span>
    
    <div class="bodycopy">
        {$record->body}
    </div>

    {filedisplayer view="`$config.filedisplay`" files=$record->expFile id=$record->id}
    
    {comments content_type="blog" content_id=$record->id title="Comments"}
</div>
