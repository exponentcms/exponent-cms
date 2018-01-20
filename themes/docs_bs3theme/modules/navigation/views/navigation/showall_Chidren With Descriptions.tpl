{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
 * Written and Designed by James Hunt
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

<div class="navigationmodule children-only">
    <h1>{$current->name}</h1>
    <ul>
        {assign var=islastdepth value="false"}
        {foreach from=$sections item=section}
            {if $section->parent == $current->id}
            {assign var=islastdepth value="true"}
                <li>
                    {if $section->active == 1}
                        <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                    {else}
                        <span class="navlink">{$section->name}</span>&nbsp;
                    {/if}
                    <div class="bodycopy">
                    <p>
                        {$section->description}
                    </p>
                    </div>
                </li>
            {/if}
        {/foreach}
        {if $islastdepth=="false"}
        {foreach from=$sections item=section}
            {if $section->parent == $current->parent}
                <li>
                    {if $section->active == 1}
                        <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                    {else}
                        <span class="navlink">{$section->name}</span>&nbsp;
                    {/if}
                </li>
            {/if}
        {/foreach}
        {/if}
    </ul>
</div>