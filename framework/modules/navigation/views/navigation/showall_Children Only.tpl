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

<div class="module navigation children-only">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {capture assign='display'}
        {$islastdepth="false"}
        {foreach from=$sections item=section}
            {if $section->parent == $current->id}
                {$islastdepth="true"}
                <li{if $section->id==$current->id || $isparent==1} class="current"{/if}>
                    {if $section->active == 1}
                       <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                    {else}
                       <span class="navlink">{$section->name}</span>&#160;
                    {/if}
               </li>
            {/if}
        {/foreach}
    {/capture}
    {if $islastdepth == 'true'}
        <h2>{$current->name}</h2>
    {/if}
    <ul>
        {$display}

        {*FIXME revert to display siblings if no children exist?*}
        {*{if $islastdepth=="false"}*}
           {*{foreach from=$sections item=section}*}
                {*{if $section->parent == $current->parent}*}
                    {*<li{if $section->id==$current->id || $isparent==1} class="current"{/if}>*}
                        {*{if $section->active == 1}*}
                            {*<a href="{$section->link}" class="navlink {if $section->id==$current->id || $isparent==1}current{/if}"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>*}
                        {*{else}*}
                            {*<span class="navlink">{$section->name}</span>&#160;*}
                        {*{/if}*}
                    {*</li>*}
                {*{/if}*}
            {*{/foreach}*}
        {*{/if}*}
    </ul>
</div>
