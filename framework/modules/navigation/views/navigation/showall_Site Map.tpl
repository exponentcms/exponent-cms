{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="navigation site-map">
    {assign var=in_action value=0}
    {if $smarty.request.module == 'navigation' && $smarty.request.action == 'manage'}
        {assign var=in_action value=1}
    {/if}
    {assign var=titlepresent value=0}
    {if $moduletitle && !$config.hidemoduletitle}
        <h1>{$moduletitle}</h1>
        {assign var=titlepresent value=1}
    {/if}
    {assign var=sectiondepth value=-1}
    {foreach from=$sections item=section}
        {assign var=parent value=0}
        {foreach from=$sections item=iSection}
            {if $iSection->parents[0] == $section->id }
                {assign var=parent value=1}
            {/if}
        {/foreach}
        {if $section->depth > $sectiondepth}
            <ul>{assign var=sectiondepth value=$section->depth}
        {elseif $section->depth == $sectiondepth}
            </li>
        {else}
            {math equation="x-y" x=$sectiondepth y=$section->depth assign=j}
            {section name=closelist loop=$j}
                </li></ul>
            {/section}
            {assign var=sectiondepth value=$section->depth}
            </li>
        {/if}
        {if $section->active == 1}
            {if  $section->id == $current->id }
                {if $parent == 1 }
                    {assign var=class value="parent current"}
                {else}
                    {if $section->depth != 0 }
                        {assign var=class value="child current"}
                    {else}
                        {assign var=class value="current"}
                    {/if}
                {/if}
            {else}
                {if $parent == 1 }
                    {assign var=class value="parent"}
                {else}
                    {if $section->depth != 0 }
                        {assign var=class value="child"}
                    {/if}
                {/if}
            {/if}
        {else}
            {assign var=class value="inactive"}
        {/if}
            {assign var=headerlevel value=$section->depth+1+$titlepresent}
        {if $section->active == 1}
            <li class="{$class} navl{$section->depth}">
            <h{$headerlevel}><a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a></h{$headerlevel}>
        {else}
            <li class="{$class}">
            <h{$headerlevel}><span class="inactive">{$section->name}</span></h{$headerlevel}>
        {/if}
    {/foreach}
</div>
