{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div class="module help side-column childview">
    <ul>
        {foreach from=$page->records item=item name=docs}
            {if $item->children}
                <li{if $item->sef_url==$smarty.get.title} class="current"{/if}>
                    <a href={link action=show version=$item->help_version->version title=$item->sef_url src=$item->loc->src}>{$item->title}</a>
                    {$params.parent = $item->id}
                    {showmodule controller=help action=showall view=side_childview source=$item->loc->src params=$params}
                </li>
            {else}
                <li{if $item->sef_url==$smarty.get.title} class="current"{/if}>
                    <a href={link action=show version=$item->help_version->version title=$item->sef_url src=$item->loc->src}>{$item->title}</a>
                </li>
            {/if}
        {/foreach}
    </ul>
</div>
