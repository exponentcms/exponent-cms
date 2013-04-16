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

{if !empty($extra_data)}
    <div class="extra-data">
        <strong>{'Registering the following people for this event'|gettext}</strong>
        <ul>
            {foreach from=$extra_data item=person}
                <li>
                    {$person.name}{if !empty({$person.phone})}, {$person.phone}{/if}{if !empty({$person.email})}, {$person.email}{/if}
                </li>
            {/foreach}
        </ul>
    </div>
{/if}
