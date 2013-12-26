{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module expTags show">
	<h1>{"Items Tagged with"|gettext} '{$tag}'</h1>
	{permissions}
    	{if $permissions.create}
    		{*<a class="add" href="{link controller=$model_name action=create}">{"Create a new Tag"|gettext}</a>*}
    	{/if}
        {if $permissions.manage}
            {icon controller=expTag action=manage text="Manage Tags"|gettext}
        {/if}
    {/permissions}
    {br}
    {permissions}
        {if $permissions.edit}
            {icon controller=$controller action=edit record=$record title="Edit this tag"|gettext}
        {/if}
        {if $permissions.delete}
            {icon controller=$controller action=delete record=$record title="Delete this tag"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this tag?"|gettext)|cat:"');"}
        {/if}
    {/permissions}
    {br}
    <div>
        {foreach from=$record->attached item="type" key=key name=types}
            <h3>{$key|capitalize} {'items'|gettext}</h3>
            <ul>
                {foreach from=$type item=ai name=ai}
                    <li>
                        {if !empty($ai->sef_url)}
                            <a href="{link controller=$key action="show" title=$ai->sef_url}" title="{$ai->title}">{$ai->title|truncate:50:"..."}</a>
                        {else}
                            <a href="{link controller=$key action="show" id=$ai->id}" title="{$ai->title}">{$ai->title|truncate:50:"..."}</a>
                        {/if}
                    </li>
                {/foreach}
            </ul>
        {foreachelse}
            {'No items tagged with'|gettext} '{$tag}'.
        {/foreach}
    </div>
</div>
