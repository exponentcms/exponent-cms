{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="showallmotd" corecss="tables"}

{/css}

<div class="module motd showall">
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle|default:"Messages by day"|gettext}</{$config.heading_level|default:'h1'}>{/if}
    <div class="bodycopy">
        {$record->body}
    </div>
    {pagelinks paginate=$page top=1}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit text="Add a New Message"|gettext}
			{/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    <table id="prods" class="exp-skin-table">
		<thead>
			<tr>
				{$page->header_columns}
                <th>
                    {permissions}
                    {'Actions'|gettext}
                    {/permissions}
                </th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
                <tr class="{cycle values="odd,even"}">
                    <td>
                        {if empty($listing->month)}
                            {'Any Month'|gettext}
                        {else}
                            {date('M', strtotime('2017-'|cat:$listing->month|cat:'-01'))}
                        {/if}
                        {$listing->day}
                    </td>
                    <td>{$listing->body}</td>
                    <td>
                        {permissions}
                            <div class="item-actions">
                                {if $permissions.edit || ($permissions.create && $listing->poster == $user->id)}
                                    {if $myloc != $listing->location_data}
                                        {if $permissions.manage}
                                            {icon action=merge id=$listing->id title="Merge Aggregated Content"|gettext}
                                        {else}
                                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                        {/if}
                                    {/if}
                                    {icon action=edit record=$listing title="Edit this message"|gettext}
                                {/if}
                                {if $permissions.delete || ($permissions.create && $listing->poster == $user->id)}
                                    {icon action=delete record=$listing title="Delete this message"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this message?"|gettext)|cat:"');"}
                                {/if}
                            </div>
                        {/permissions}
                    </td>
                </tr>
			{foreachelse}
				<tr class="{cycle values="odd,even"}">
                    <td colspan="6">
                        {permissions}
                           {if $permissions.create}
                               {message class=notice text="There are no messages yet."|gettext}
                           {/if}
                       {/permissions}
                    </td>
                </tr>
			{/foreach}
		</tbody>
    </table>
    {pagelinks paginate=$page bottom=1}
    {permissions}
        {if $permissions.manage}
            <div class="module-actions">
                {icon class=view action=showall_year text="View Tips as Calendar"|gettext}
            </div>
        {/if}
    {/permissions}
</div>
