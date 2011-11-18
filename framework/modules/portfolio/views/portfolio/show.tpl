{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
	<h1>{$record->title}</h1>
	{permissions}
		<div class="item-actions">
			{if $permissions.edit == 1}
				{icon action=edit record=$record title="edit `$record->title`"}
			{/if}
		</div>
	{/permissions}
	
	{if $record->expTag}
		<div class="tag">
			{'Tags'|gettext}:
			{foreach from=$record->expTag item=tag name=tags}
				<a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
			{/foreach}
		</div>
	{/if}

	<div class="bodycopy">
        {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
		{$record->body}
	</div>
</div>
