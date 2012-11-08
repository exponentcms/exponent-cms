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

{css unique="mega" link="`$asset_path`css/megamenu.css"}

{/css}
{if empty($config.maxwidth) || $config.maxwidth < 1}
    {$maxwidth = 3}
{elseif $config.maxwidth > 5}
    {$maxwidth = 5}
{else}
    {$maxwidth = $config.maxwidth}
{/if}
<div class="module navigation mega">
    <ul id="menu">
        {getnav type='hierarchy' notyui=1 assign=hierarchy}
        {foreach name="children" key=key from=$hierarchy item=parent}
            {if empty($parent->parents)}
                {if $key!=0}</li>{/if}
                {if $parent->id == $current->id || in_array($parent->id,$current->parents)}
                    {$class = ' current'}
                {else}
                    {$class = ''}
                {/if}
                {*top level menu*}
                {if $parent->type != 3}
                    <li class="drop{$class}"><a {if $parent->url != "#"}href="{$parent->url}"{/if}{if !empty($parent->itemdata)} class="drop"{/if}{if $parent->new_window} target="_blank"{/if}>{if !empty($parent->expFile[0]->id)}{img class=img_left file_id=$parent->expFile[0]->id w=16 h=16} {/if}{$parent->text}</a>
                {else}
                    <li class="drop{$class}"><a class="drop">{if !empty($parent->expFile[0]->id)}{img class=img_left file_id=$parent->expFile[0]->id w=16 h=16} {/if}{$parent->text}</a>
                {/if}
                {if !empty($parent->itemdata) &&  $parent->type != 3}
                    {if $config.height && $parent->maxdepth == 1}
                        {$columns = ceil($parent->maxitems / $config.height)}
                        {if $maxwidth > $columns}{$width = $columns}{else}{$width = $maxwidth}{/if}
                    {else}
                        {if $maxwidth > $parent->maxitems}{$width = $parent->maxitems}{else}{$width = $maxwidth}{/if}
                    {/if}
                    {$dropsize = "dropdown_`$width`column"|plural:$width}
                    <div class="{$dropsize}">
                        {function menu_items depth = 0}
                            {foreach from=$parent->itemdata item=child}
                                {*{if $depth > 0}*}
                                    {*<div class="col_{$depth}">&#160;</div>*}
                                {*{/if}*}
                                {if $child->id == $current->id}
                                    {$class = 'class="current" '}
                                {else}
                                    {$class = ''}
                                {/if}
                                {if !empty($child->description)}
                                    {$description = "<p class='description'>`$child->description`</p>"}
                                {else}
                                    {$description = ''}
                                {/if}
                                {if !$depth}<div class="col_1{if !empty($child->itemdata)} column greybox{/if}">{/if}
                                    <div class="menuitem{if !empty($descriptioin)} desc{/if}{if !empty($child->itemdata)} menuheader{/if}"><a {$class}{if $child->url != "#"}href="{$child->url}"{/if}{if $child->new_window} target="_blank"{/if}><h4>{if !empty($child->expFile[0]->id)}{img class=img_left file_id=$child->expFile[0]->id w=24 h=24}{/if}{if $config.usetitle && !empty($child->title)}{$child->title}{else}{$child->text}{/if}</h4>{if $config.usedesc}{$description}{/if}</a></div>
                                    {if !empty($child->itemdata)}
                                    <div class="child">
                                        {menu_items parent = $child depth=$depth+1}
                                    </div>
                                    {/if}
                                {if !$depth}</div>{/if}
                            {/foreach}
                        {/function}
                        {menu_items child = $child}
                    </div>
                {elseif $parent->type == 3}
                    {if empty($parent->width) || $parent->width < 1}
                        {$width = 3}
                    {elseif $parent->width > 5}
                        {$width = 5}
                    {else}
                        {$width = $parent->width}
                    {/if}
                    {$dropsize = "dropdown_`$width`column"|plural:$width}
                    <div class="{$parent->class} {$dropsize}">
                        {showmodule module='container' view="Default" source="menuitem-"|cat:$parent->id chrome=true}
                    </div>
                {/if}
            {/if}
        {/foreach}
        </li>
    </ul>
</div>
{clear}
