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

<div class="module text headline headline-show">
    <div class="item{if !$items[0]->approved} unapproved{/if}">
        {if $items[0]->title}<{$config.heading_level|default:'h1'}>{$items[0]->title}</{$config.heading_level|default:'h1'}>{/if}
    </div>
    {permissions}
        <div class="module-actions">
            {if $permissions.edit || ($permissions.create && $items[0]->poster == $user->id)}
                {if $items[0]->revision_id > 1}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$items[0]->revision_id}">{$items[0]->revision_id}</span>{/if}
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
</div>
