{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

<div class="module help side-column">
    {if $moduletitle}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}

    <ul>
    {foreach from=$page->records item=doc name=docs}
        <li{if $doc->sef_url==$smarty.get.title} class="current"{/if}>
            <a href={link action=show version=$doc->help_version->version title=$doc->sef_url src=$doc->loc->src}>{$doc->title}</a>
        </li>
        {if $doc->children}
            {$params.parent = $doc->id}
            {showmodule controller=help action=showall view=side_childview source=$doc->loc->src params=$params}
        {/if}
    {/foreach}
    </ul>
</div>
