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

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}

<div class="module blog show">
    <h1>{$record->title}</h1>
    {permissions}
        <div class="item-actions">
            {if $permissions.edit == 1}
                {icon action=edit record=$record}
            {/if}
            {if $permissions.delete == 1}
                {icon action=delete record=$record}
            {/if}
            {if $permissions.manage == 1}
                {icon controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
        </div>
    {/permissions}
    <div class="post-info">
        <span class="attribution">
            {'Posted by'|gettext} <a href="{link action=showall_by_author author=$record->poster|username}">{attribution user_id=$record->poster}</a> {'on'|gettext} <span class="date">{$record->created_at|format_date}</span>
        </span>
        | <a class="comments" href="{link action=show title=$record->sef_url}#exp-comments">{$record->expComment|@count} {"Comments"|gettext}</a>
		{if $record->expTag[0]->id}
		| <span class="tags">
			{"Tags"|gettext}: 
			{foreach from=$record->expTag item=tag name=tags}
                <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>
                {if $smarty.foreach.tags.last != 1},{/if}
			{/foreach} 
		</span>
		{/if}
    </div>
    <div class="bodycopy">
        {if $config.filedisplay != "Downloadable Files"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
        {/if}
        {$record->body}
        {if $config.filedisplay == "Downloadable Files"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
        {/if}
    </div>
    {comments content_type="blog" content_id=$record->id title="Comments"|gettext}
</div>
