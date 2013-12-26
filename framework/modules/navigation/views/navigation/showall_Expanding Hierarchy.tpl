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

{css unique="expanding-hierarchy" link="`$asset_path`css/depth.css"}

{/css}

<div class="module navigation expanding expanding-hierarchy">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <ul>
    {foreach from=$sections item=section}
        {$commonParent=0}
        {foreach from=$current->parents item=parentId}
            {if $parentId == $section->id || $parentId == $section->parent}
                {$commonParent=1}
            {/if}
        {/foreach}
        {if $section->numParents == 0 || $commonParent || $section->id == $current->id || $section->parent == $current->id}
            <li class="depth{$section->depth} {if $section->id == $current->id}current{/if}">
                {if $section->active == 1}
                    <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>&#160;
                {else}
                    <span class="navlink">{$section->name}</span>&#160;
                {/if}
            </li>
        {/if}
    {/foreach}
    <ul>
</div>
