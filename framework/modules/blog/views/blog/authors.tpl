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

<div class="module blog showall-authors">
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle|default:"Authors"|gettext}</{$config.heading_level|default:'h2'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <ul>
        {foreach from=$authors item=author}
            <li>
                <a href="{link action=showall_by_author author=$author->username}" title='{"View all posts by"|gettext} {$author->firstname} {$author->lastname}'>{$author->firstname} {$author->lastname} ({$author->count})</a>
            </li>
        {/foreach}
    </ul>
</div>
