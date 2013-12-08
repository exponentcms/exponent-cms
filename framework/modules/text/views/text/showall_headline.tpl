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

<div class="module text headline headline-show">
    <div class="item">
        {if $items[0]->title}<{$config.heading_level|default:'h1'}>{$items[0]->title}</{$config.heading_level|default:'h1'}>{/if}
    </div>
    {permissions}
        <div class="module-actions">
            {if $permissions.edit || ($permissions.create && $items[0]->poster == $user->id)}
                {icon action=edit record=$items[0]}
            {/if}
            {if $permissions.delete || ($permissions.create && $items[0]->poster == $user->id)}
                {icon action=delete record=$items[0]}
            {/if}
        </div>
    {/permissions}
</div>
