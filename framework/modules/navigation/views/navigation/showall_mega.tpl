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
{if empty($config.width)}
    {$dropsize = "dropdown_3columns"}
    {$width = 3}
{elseif $config.width == 1}
    {$dropsize = "dropdown_1column"}
    {$width = $config.width}
{elseif $config.width > 5}
    {$dropsize = "dropdown_5columns"}
    {$width = 5}
{else}
    {$dropsize = "dropdown_`$config.width`columns"}
    {$width = $config.width}
{/if}
<div class="module navigation mega">
    <ul id="menu">
        {getnav type='hierarchy' assign=hierarchy}
        {foreach name="children" key=key from=$hierarchy item=page}
            {if empty($page->parents)}
                {if $key!=0}</li>{/if}
                {if $page->id == $current->id || in_array($page->id,$current->parents)}
                    {$class = ' current'}
                {else}
                    {$class = ''}
                {/if}
                <li class="drop{$class}"><a {if $page->url != "#"}href="{$page->url}"{/if}{if !empty($page->itemdata)} class="drop"{/if}{if $page->new_window} target="_blank"{/if}>{if !empty($page->expFile[0]->id)}{img class=img_left file_id=$page->expFile[0]->id w=16 h=16} {/if}{$page->text}</a>
                {if !empty($page->itemdata)}
                    <div class="{$dropsize}">
                        {foreach from=$page->itemdata item=child}
                            {function menu_items depth = 0}
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
                                {if empty($child->submenu)}
                                    <div class="col_{$width}">
                                        <a {$class}{if $page->url != "#"}href="{$child->url}"{/if}{if $child->new_window} target="_blank"{/if}><h3>{if !empty($child->expFile[0]->id)}{img class=img_left file_id=$child->expFile[0]->id w=24 h=24}{/if}{if $config.usetitle && !empty($child->title)}{$child->title}{else}{$child->text}{/if}</h3>{if $config.usedesc}{$description}{/if}</a>
                                    </div>
                                {else}
                                    <div class="col_{$width}">
                                        <a {$class}{if $page->url != "#"}href="{$child->url}"{/if}{if $child->new_window} target="_blank"{/if}><h3>{if !empty($child->expFile[0]->id)}{img class=img_left file_id=$child->expFile[0]->id} {/if}{if $config.usetitle && !empty($child->title)}{$child->title}{else}{$child->text}{/if}</h3>{if $config.usedesc}{$description}{/if}</a>
                                    </div>
                                    {*{if $depth > 0}*}
                                        {*<div class="col_{$depth}">&#160;</div>*}
                                    {*{/if}*}
                                    {foreach from=$child->submenu->itemdata item=submenu}
                                        {if $submenu->id == $current->id}
                                            {$class = ' class="current"'}
                                        {else}
                                            {$class = ''}
                                        {/if}
                                        {if !empty($submenu->description)}
                                            {$description = "<p class='description'>`$submenu->description`</p>"}
                                        {else}
                                            {$description = ''}
                                        {/if}
                                        {if !empty($submenu->submenu)}
                                            {menu_items child = $submenu depth=$depth+1}
                                        {else}
                                            <div class="col_1">
                                                <a {$class}{if $page->url != "#"}href="{$submenu->url}"{/if}{if $submenu->new_window} target="_blank"{/if}><h4>{if !empty($submenu->expFile[0]->id)}{img class=img_left file_id=$submenu->expFile[0]->id} {/if}{if $config.usetitle && !empty($submenu->title)}{$submenu->title}{else}{$submenu->text}{/if}</h4>{if $config.usedesc}{$description}{/if}</a>
                                            </div>
                                        {/if}
                                    {/foreach}
                                {/if}
                            {/function}
                            {menu_items child = $child}
                        {/foreach}
                    </div>
                {/if}
            {/if}
        {/foreach}
        </li>
    </ul>
</div>
{clear}
