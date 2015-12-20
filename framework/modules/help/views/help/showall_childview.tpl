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

<div class="module help showall childview">
    <{$config.item_level|default:'h2'}>{'Additional Help Topics'|gettext}</{$config.item_level|default:'h2'}>
    <dl>
    {$myloc=serialize($__loc)}
    {foreach from=$page->records item=item name=docs}
        <div class="item">
            <dt>
                <h3>
                    <a href={link controller=help action=show version=$item->help_version->version title=$item->sef_url} title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                </h3>
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
            
        </div>
    {/foreach}
    </dl>
</div>
