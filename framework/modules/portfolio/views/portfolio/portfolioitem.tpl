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

<div class="item">
	<h3><a href="{link action=show title=$record->sef_url}" title="{$record->body|summarize:"html":"para"}">{$record->title}</a></h3>
	{permissions}
		<div class="item-actions">
			{if $permissions.edit == 1}
				{icon action=edit record=$record title="Edit `$record->title`"}
			{/if}
			{if $permissions.delete == 1}
				{icon action=delete record=$record title="Delete `$record->title`"}
			{/if}
		</div>
	{/permissions}
	{if $record->expTag|@count>0 && !$config.disabletags}
		<div class="tags">
			{'Tags'|gettext}:
			{foreach from=$record->expTag item=tag name=tags}
				<a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
			{/foreach}
		</div>
	{/if}
    <div class="bodycopy">
        {if $config.filedisplay != "Downloadable Files"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record is_listing=1}
        {/if}
        {if $config.usebody==1}
            <p>{$record->body|summarize:"html":"paralinks"}</p>
        {elseif $config.usebody==2}
        {else}
            {$record->body}
        {/if}
        {if $config.filedisplay == "Downloadable Files"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record is_listing=1}
        {/if}
    </div>
    {clear}
    {permissions}
        {if $permissions.create == 1}
            <div class="module-actions">
                {icon class="add addhere" action=edit rank=$record->rank+1 title="Add another here"|gettext  text="Add a portfolio piece here"|gettext}
            </div>
        {/if}
    {/permissions}
</div>