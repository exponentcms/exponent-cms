{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

<div class="module navigation siblings-only">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<{$config.item_level|default:'h2'}>{$current->name}</{$config.item_level|default:'h2'}>
    <ul>
        {foreach from=$sections item=section}
            {if $section->parent == $current->parent}
                <li>
                    {if $section->active == 1}
                        <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                    {else}
                        <span class="navlink">{$section->name}</span>&#160;
                    {/if}
                </li>
            {/if}
        {/foreach}
    </ul>
</div>
