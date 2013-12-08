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

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module blog showall-headlines">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit text="Add a new blog article"|gettext}
			{/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='blog' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='blog' text="Manage Categories"|gettext}
                {/if}
            {/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <ul>
        {foreach from=$page->records item=record name="blogs"}
            {if $smarty.foreach.blogs.iteration <= $config.headcount}
                <li class="item">
                    <a href="{link action=show title=$record->sef_url}" title="{$record->body|summarize:"html":"para"}">{$record->title}</a>
                    {if !$config.displayauthor}
                        <span class="label posted"> {'by'|gettext} </span>
                        <a href="{link action=showall_by_author author=$record->poster|username}">{attribution user_id=$record->poster}</a>
                    {/if}
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                                {if $myloc != $record->location_data}
                                    {if $permissions.manage}
                                        {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit record=$record}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                                {icon action=delete record=$record}
                            {/if}
                        </div>
                    {/permissions}
                </li>
            {/if}
        {/foreach}
    </ul> 
</div>
