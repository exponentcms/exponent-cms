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

{css unique="expanding-hierarchy-children" link="`$asset_path`css/depth.css"}

{/css}

<div class="module navigation expanding expanding-hierarchy-children-only">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <ul>
        {foreach from=$sections item=section}
            {if $section->numParents != 0}
                {$commonParent=0}
                {$isParent=0}
                {foreach from=$current->parents item=parentId}
                    {if $parentId == $section->id}
                        {$isParent=1}
                    {/if}
                    {if $parentId == $section->id || $parentId == $section->parent}
                        {$commonParent=1}
                    {/if}
                {/foreach}
                {if $section->numParents == 0 || $commonParent || $section->id == $current->id ||  $section->parent == $current->id}
                    {if $section->numParents == 1 && $isParent == 0 && $current->id != $section->id}
                        <li class="depth{$section->depth}">
                    {elseif $section->numParents == 1}
                        <li class="depth{$section->depth}{if $current->id == $section->id} current{/if}">
                    {else}
                        <li class="depth{$section->depth}">
                    {/if}

                        {if $section->active == 1 && $section->id == $current->id}
                            <a href="{$section->link}" class="active" {if $section->new_window} target="_blank"{/if}>{$section->name}</a>&#160;
                        {elseif $section->active == 1}
                            <a href="{$section->link}" {if $section->new_window} target="_blank"{/if}>{$section->name}</a>&#160;
                        {else}
                            <span class="navlink">{$section->name}</span>&#160;
                        {/if}
                    </li>
                {/if}
            {/if}
        {/foreach}
    </ul>
</div>
