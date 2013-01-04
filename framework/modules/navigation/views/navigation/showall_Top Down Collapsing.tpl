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

{css unique="depth" link="`$asset_path`css/depth.css"}
    
{/css}

<div class="module navigation collapsing top-down-collapsing">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {if $current->parent!="-1"}
        {capture assign=list}
            <ul>
                {foreach from=$sections item=section}
                    {if in_array($section->parent,$current->parents) || in_array($section->id,$current->parents) || $section->id == $current->id ||  $section->parent == $current->id ||  ($ection->depth !=0 && $section->parent == $current->parent)}
                        {if $section->depth==0}{capture assign=top}{$section->name}{/capture}{else}
                            <li class="depth{$section->depth} {if $section->id == $current->id}current{/if}">
                                {if $section->active == 1}
                                    <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                                {else}
                                    <span class="navlink">{$section->name}</span>
                                {/if}
                            </li>
                        {/if}
                    {/if}
                {/foreach}
            </ul>
        {/capture}
        <h2>{$top}</h2>
        {$list}
    {else}
        <h2>{$current->name}</h2>
    {/if}
</div>
