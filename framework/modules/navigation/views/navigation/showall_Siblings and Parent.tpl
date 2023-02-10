{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="siblings-and-parents"}
{literal}
    .siblings-and-parents li {
        list-style: none;
        line-height: 3rem;
        margin-bottom: 10px;
        font-size: 120%;
    }
    .siblings-and-parents .parent {
        font-size: 130%;
        font-weight: bold;
    }
    .siblings-and-parents .current a {
        color: black;
        font-weight: bolder;
    }
{/literal}
{/css}

<div class="module navigation siblings-and-parents">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
{*    <{$config.item_level|default:'h2'}>{$current->name}</{$config.item_level|default:'h2'}>*}
    <ul>
        {foreach from=$sections item=section}
            {if $section->parent == $current->parent || $section->id == $current->parent}
                {if ($section->parent == 0 && $section->id == $current->id) || ($section->parent == 0 && $section->id == $current->parent) || ($section->parent != 0 && $section->parent == $current->parent)}
                <li class="{if $section->id == $current->id}current{/if} {if $section->parent == 0}parent{/if}">
                    {if $section->active == 1}
                        <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                    {else}
                        <span class="navlink">{$section->name}</span>&#160;
                    {/if}
                </li>
                {/if}
                {if $section->id == $current->id}
                    {getnav type="children" of=$section->id assign=kids}
                    {if $kids}
                        {foreach from=$kids item=child}
                            <li>
                                {if $child->active == 1}
                                    <a href="{$child->link}" class="navlink"{if $child->new_window} target="_blank"{/if}>{$child->name}</a>
                                {else}
                                    <span class="navlink">{$child->name}</span>&#160;
                                {/if}
                            </li>
                        {/foreach}
                    {/if}
                {/if}
            {/if}
        {/foreach}
    </ul>
</div>
