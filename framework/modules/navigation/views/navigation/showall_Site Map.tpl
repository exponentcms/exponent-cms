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

<div class="module navigation site-map">
    {$titlepresent=0}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}
        <{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>
        {$titlepresent=1}
    {/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$in_action=0}
    {if $smarty.request.module == 'navigation' && $smarty.request.action == 'manage'}
        {$in_action=1}
    {/if}
    {$sectiondepth=-1}
    {foreach from=$sections item=section}
        {$parent=0}
        {foreach from=$sections item=iSection}
            {if $iSection->parents[0] == $section->id }
                {$parent=1}
            {/if}
        {/foreach}
        {if $section->depth > $sectiondepth}
            <ul>{$sectiondepth=$section->depth}
        {elseif $section->depth == $sectiondepth}
            </li>
        {else}
            {$j=$sectiondepth-$section->depth}
            {section name=closelist loop=$j}
                </li></ul>
            {/section}
            {$sectiondepth=$section->depth}
            </li>
        {/if}
        {if $section->active == 1}
            {if  $section->id == $current->id }
                {if $parent == 1 }
                    {$class="parent current"}
                {else}
                    {if $section->depth != 0 }
                        {$class="child current"}
                    {else}
                        {$class="current"}
                    {/if}
                {/if}
            {else}
                {if $parent == 1 }
                    {$class="parent"}
                {else}
                    {if $section->depth != 0 }
                        {$class="child"}
                    {/if}
                {/if}
            {/if}
        {else}
            {$class="inactive"}
        {/if}
        {$headerlevel=$section->depth+1+$titlepresent}
        {if $section->active == 1}
            <li class="{$class} navl{$section->depth}">
            <h{$headerlevel}><a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a></h{$headerlevel}>
        {else}
            <li class="{$class}">
            <h{$headerlevel}><span class="inactive">{$section->name}</span></h{$headerlevel}>
        {/if}
    {/foreach}
    {permissions}
        {if $canManage == 1}
            {icon action=manage}
        {/if}
    {/permissions}
</div>
