{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{uniqueid prepend="links" assign="name"}

{css unique="links" link="`$asset_path`css/links.css"}

{/css}

<div class="module links showall-quicklinks">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit text="Add a new link"|gettext}
			{/if}
			{if $permissions.manage}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='links' text="Manage Categories"|gettext}
                {/if}
                {*{if $rank == 1}*}
                {if $config.order == 'rank'}
				    {ddrerank items=$items model="links" label="Links"|gettext}
                {/if}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    {if $config.usecategories}
        {foreach from=$cats key=catid item=cat}
            {if $catid != 0}
               <div class="itemtitle"><h3>{$cat->name}</h3></div>
            {/if}
            <ul>
            {foreach name=links from=$cat->records item=item}
                <li class="item{if $smarty.foreach.links.last} last{/if}">
                    <div class="link">
                        <a class="{$cat->color}" href="{$item->url}" {if $item->new_window == 1} target="_blank"{/if} title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                    </div>
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                {if $myloc != $item->location_data}
                                    {if $permissions.manage}
                                        {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit record=$item}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                {icon action=delete record=$item}
                            {/if}
                        </div>
                    {/permissions}
                </li>
            {foreachelse}
                {if ($catid != 0) }
                    <div ><em>{'No Links'|gettext}</em></div>
                {/if}
            {/foreach}
            </ul>
        {/foreach}
    {else}
        <ul>
            {foreach name=items from=$items item=item name=links}
                <li class="item{if $smarty.foreach.links.last} last{/if}">
                    <a class="link" {if $item->new_window}target="_blank"{/if} href="{$item->url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                {if $myloc != $item->location_data}
                                    {if $permissions.manage}
                                        {icon img='arrow_merge.png' action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit text='notext' record=$item}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                {icon action=delete text='notext' record=$item}
                            {/if}
                        </div>
                    {/permissions}
                </li>
            {/foreach}
        </ul>
    {/if}
</div>
