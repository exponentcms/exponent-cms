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

<div class="module blog showall-dates">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {foreach from=$dates item=ydate key=year}
        <h3>{$year}</h3>
        <ul>
            {foreach from=$ydate item=mdate key=month}
                <li>
                    <a href="{link action=showall_by_date month=$month year=$year}" title='{"View all posts from"|gettext} {$mdate->name} {$year}'>{$mdate->name} ({$mdate->count})</a>
                </li>
            {/foreach}
        </ul>
    {/foreach}
</div>
