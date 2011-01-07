{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

<div class="module blog showall-authors">
    <h2>{$moduletitle|default:"Authors"}</h2>

    <ul>
        {foreach from=$authors item=author}
            <li>
                <a href="{link action=showall_by_author author=$author->username}">{$author->firstname} {$author->lastname} ({$author->count})</a>
            </li>
        {/foreach}
    </ul>
</div>
