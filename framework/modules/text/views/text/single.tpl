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

<div class="module text single">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {$myloc=serialize($__loc)}
    {if $items[0]->title}<h2>{$items[0]->title}</h2>{/if}
    {permissions}
        {if $permissions.edit == 1}
        <div class="item-actions">
            {if $myloc != $items[0]->location_data}
                {if $permissions.manage == 1}
                    {icon action=merge id=$items[0]->id title="Merge Aggregated Content"|gettext}
                {else}
                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                {/if}
            {/if}
            {icon action=edit record=$items[0]}
        </div>
        {/if}
    {/permissions}
    <div class="bodycopy">
        {$items[0]->body}
    </div>
</div>
