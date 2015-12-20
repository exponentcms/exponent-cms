{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="module help showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class=add action=edit text="Add a Help Doc"|gettext}{br}
            {/if}
            {if $permissions.manage}
                {icon action=manage version=$current_version->id text="Manage Help Docs for version"|gettext|cat:" `$current_version->version`"}{br}
                {icon class=manage action=manage_versions text="Manage Help Versions"|gettext}{br}
                {*{if $rank == 1}*}
                {if $rank}
                    {ddrerank items=$page->records only="help_version_id=`$current_version->id`" model="help" label="Help Docs"|gettext}
                {/if}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <dl>
    {$myloc=serialize($__loc)}
    {foreach from=$page->records item=item name=docs}
        <div class="item">
            <dt>
                <{$config.item_level|default:'h2'}>
                    <a href={link controller=help action=show version=$item->help_version->version title=$item->sef_url} title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                </{$config.item_level|default:'h2'}>
            </dt>
            
            <dd>
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
                    {icon action=copy record=$item}
                {/if}
                {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                    {icon action=delete record=$item}
                {/if}
            </div>
            {/permissions}
            
            <div class="bodycopy">
                {*{$item->summary}*}
                {*{$item->body|summarize:"html":"paralinks"}*}
                {$item->body|summarize:"html":"parahtml"}
            </div>

            {if $item->children}
                {$params.parent = $item->id}
                {showmodule controller=help action=showall view=side_childview source=$item->loc->src params=$params}
            {/if}
        </div>
    {/foreach}
    </dl>
</div>
