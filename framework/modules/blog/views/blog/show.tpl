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
    {assign var=myloc value=serialize($__loc)}
    {permissions}
        <div class="item-actions">
            {if $myloc != $record->location_data}{icon class=merge img='arrow_merge.png' title="Aggregated Content"|gettext}{/if}
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
            {if $record->private}<strong>({'Draft'|gettext})</strong>{/if}
            {if $record->publish > $smarty.now}
                <strong>{'Will be'|gettext}&nbsp;
            {elseif ($record->unpublish != 0) && $record->unpublish <= $smarty.now}
                <strong>{'Was'|gettext}&nbsp;
            {/if}
            {'Posted by'|gettext} <a href="{link action=showall_by_author author=$record->poster|username}">{attribution user_id=$record->poster}</a> {'on'|gettext} <span class="date">{$record->publish|format_date}</span>
            {if $record->publish > $smarty.now}
                </strong>&nbsp;
            {elseif ($record->unpublish != 0) && $record->unpublish <= $smarty.now}
                {'now unpublished'|gettext}</strong>&nbsp;
            {/if}
        </span>
        | <a class="comments" href="{link action=show title=$record->sef_url}#exp-comments">{$record->expComment|@count} {"Comments"|gettext}</a>
		{if $record->expTag|@count>0 && !$config.disabletags}
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
