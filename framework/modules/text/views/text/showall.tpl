{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

<div class="module text showall">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create == 1}
                {icon class=add action=edit rank=1 title="Add text to the top" text="Add text at the top"|gettext}
            {/if}
            {if $permissions.manage == 1}
                {ddrerank items=$items model="text" label="Text Items"|gettext}
            {/if}
        </div>
    {/permissions}
    {foreach from=$items item=text name=items}
        {if $text->title}<h2>{$text->title}</h2>{/if}
        {permissions}
			<div class="item-actions">
				{if $permissions.edit == 1}
					{icon action=edit class="edit" record=$text title="Edit this `$modelname`"}
				{/if}
				{if $permissions.delete == 1}
					{icon action=delete record=$text title="Delete this Text Item" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
				{/if}
			</div>
        {/permissions}
        <div class="bodycopy">
            {filedisplayer view="`$config.filedisplay`" files=$text->expFile id=$text->id}
            {$text->body}
        </div>
        {permissions}
			<div class="module-actions">
				{if $permissions.create == 1}
					{icon class=add action=edit rank=`$text->rank+1` title="Add more text here" text="Add more text here"}
				{/if}
			</div>
        {/permissions}
        {clear}
    {/foreach}
</div>
