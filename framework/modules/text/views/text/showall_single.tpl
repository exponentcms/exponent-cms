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

<div class="module text single">
    {if $moduletitle && !(!empty($config.hidemoduletitle) xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {$myloc=serialize($__loc)}
    <div class="item{if !$items[0]->approved} unapproved{/if}">
        {if $items[0]->title}<{$config.item_level|default:'h2'}>{$items[0]->title}</{$config.item_level|default:'h2'}>{/if}
        {permissions}
           <div class="item-actions">
                {if $permissions.edit || ($permissions.create && $items[0]->poster == $user->id)}
                    {if $items[0]->revision_id > 1}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$items[0]->revision_id}">{$items[0]->revision_id}</span>{/if}
                    {if $myloc != $items[0]->location_data}
                        {if $permissions.manage}
                            {icon action=merge id=$items[0]->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
                    {icon action=edit record=$items[0]}
                {/if}
                {if $permissions.delete || ($permissions.create && $items[0]->poster == $user->id)}
                    {icon action=delete record=$items[0]}
                {/if}
               {if !$items[0]->approved && $permissions.approve && $permissions.edit}
                   {icon action=approve record=$items[0]}
               {/if}
            </div>
        {/permissions}
        <div class="bodycopy">
            {if $config.ffloat != "Below"}
                {filedisplayer view="`$config.filedisplay`" files=$items[0]->expFile record=$items[0]}
            {/if}
            {$items[0]->body}
            {if $config.ffloat == "Below"}
                {filedisplayer view="`$config.filedisplay`" files=$items[0]->expFile record=$items[0]}
            {/if}
        </div>
    </div>
    {clear}
</div>
