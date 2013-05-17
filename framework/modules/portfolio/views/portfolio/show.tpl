{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
    {printer_friendly_link}{export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}{br}
    {$myloc=serialize($__loc)}
	{permissions}
		<div class="item-actions">
			{if $permissions.edit == 1}
                {if $myloc != $record->location_data}
                    {if $permissions.manage == 1}
                        {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                    {else}
                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                    {/if}
                {/if}
				{icon action=edit record=$record title="edit `$record->title`"}
			{/if}
            {if $permissions.delete == 1}
                {icon action=delete record=$record title="Delete `$record->title`"}
            {/if}
		</div>
	{/permissions}
    {tags_assigned record=$record}
	<div class="bodycopy">
        {if $config.ffloat != "Below"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
        {/if}
		{$record->body}
        {if $config.ffloat == "Below"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
        {/if}
	</div>
    {clear}
</div>
