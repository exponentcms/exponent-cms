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

<div class="module text single">
    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    {if $items[0]->title}<h2>{$items[0]->title}</h2>{/if}
    {permissions}
        {if $permissions.edit == 1}
        <div class="item-actions">
            {icon action=edit record=$items[0]}
        </div>
        {/if}
    {/permissions}
    <div class="bodycopy">
        {$items[0]->body}
    </div>
</div>
