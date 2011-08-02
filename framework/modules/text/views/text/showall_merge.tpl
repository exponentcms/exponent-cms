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
			{if $permissions.edit == 1}
				<div class="msg-queue notice">
				<h3>This special template allows merging multiple text modules (items) into one module</h3>
				<i>(This may be desired as a post-migration cleanup action)</i>
				<ul>
					<li>From the target module, aggregate the source module(s)</li>
					<i>(In practice, this will likely be the top text module aggregating all the other 
					text modules on the page, focus on the page names)</i>
					<li>In the target module, edit each text item,
					entering the source module's title in the edited item's title, then save</li>
					<i>(You'll notice the source modules become empty after each save)</i>
					<li>Reorder the text items to get the proper sequence</li>
					<li>When the source module is empty, remove that module</li>
				</ul>
				</div>
			{/if}
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
					{icon action=edit class="edit" id=$text->id title="Edit this `$modelname`"}
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
