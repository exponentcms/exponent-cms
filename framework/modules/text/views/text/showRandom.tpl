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

<div class="module text show-random">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {$myloc=serialize($__loc)}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class=add action=edit text="Add Text"|gettext}
            {/if}
            {if $permissions.manage}
                {br}{icon class=manage action=showall text="Manage Text Items"|gettext}
            {/if}
        </div>
    {/permissions}
    {foreach from=$items item=item}
        <div class="item">
            {if $item->title}<{$config.item_level|default:'h2'}>{$item->title}</{$config.item_level|default:'h2'}>{/if}
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
            <div class="bodycopy">
                {$item->body}
            </div>
        </div>
    {/foreach}
</div>
