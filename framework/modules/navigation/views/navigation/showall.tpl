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

<div class="module navigation default">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<ul>
        {if $config.showcurrent}
            <h2>{$current->name}</h2>
        {/if}
        {$startdepth=$current->depth}
        {if $config.showparents && $current->depth}
            {$startdepth=$current->depth-1}
        {/if}
        {if $config.showtop}
            {$startdepth=0}
        {/if}
        {if $config.allhierarchy}
            {$navtype='all'}
            {$startdepth=0}
        {elseif $config.showsiblings&&$config.showgrandchildren}
            {$navtype='siblingsandallsubchildren'}
        {elseif $config.showsiblings&&$config.showchildren}
           {$navtype='siblingsandchildren'}
        {elseif $config.showgrandchildren}
            {$navtype='allsubchildren'}
        {elseif $config.showsiblings}
            {$navtype='siblings'}
        {*{elseif $config.showchildren}*}
        {else}
            {$navtype='children'}
        {/if}
        {getnav type=$navtype of=$current->id top=$config.showtop parents=$config.showparents assign=children}
        {foreach key=skey name=children from=$children item=child}
            {if $smarty.foreach.children.first}<ul id="{$child->name|replace:' ':''}gc" class="children">{/if}
                <li style="margin-left: {($child->depth*20)-($startdepth*20)}px">
                    {if $config.styledepth}
                        <h{$child->depth-$startdepth+2}>
                    {/if}
                    {if $child->id == $current->id && $config.markcurrent}
                        <strong><img src="{$smarty.const.ICON_RELATIVE|cat:'mark.gif'}" title="{'You are here'|gettext}" alt="{'Mark'|gettext}" />
                    {/if}
                    {if $child->id != $current->id && $child->active == 1}
                        <a href="{$child->link}" class="navlink"{if $child->new_window} target="_blank"{/if} title="{$child->name}">{$child->name}</a>
                    {else}
                        <span class="navlink">{$child->name}</span>
                    {/if}
                    {if $child->id == $current->id && $config.markcurrent}
                        </strong>
                    {/if}
                    {if $config.styledepth}
                        </h{$child->depth-$startdepth+2}>
                    {/if}
                </li>
            {if $smarty.foreach.children.last}</ul>{/if}
        {/foreach}
	</ul>
</div>
