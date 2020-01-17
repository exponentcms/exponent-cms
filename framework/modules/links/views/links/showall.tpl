{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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

{uniqueid prepend="links" assign="name"}

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

{css unique="links" link="`$asset_path`css/links.css"}

{/css}

<div class="module links showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit text="Add a new link"|gettext}
			{/if}
			{if $permissions.manage}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='links' text="Manage Categories"|gettext}
                {/if}
                {*{if $rank == 1}*}
                {if $config.order == 'rank'}
				    {ddrerank items=$items model="links" label="Links"|gettext}
                {/if}
			{/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    {if $config.usecategories}
        {foreach from=$cats key=catid item=cat}
            {if $catid != 0}
                <h2 class="category">{$cat->name}</h2>
            {/if}
            {foreach name=items from=$cat->records item=item}
                <div class="item">
                    <h3 class="link-title"><a class="{$cat->color}"{if $item->new_window} target="_blank"{/if} href="{$item->url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></h3>
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                {if $myloc != $item->location_data}
                                    {if $permissions.manage}
                                        {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit record=$item}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                {icon action=delete record=$item}
                            {/if}
                        </div>
                    {/permissions}
                    {if $item->expFile[0]->id}
                        <a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{img file_id=$item->expFile[0]->id w=200 h=150 constrain=1 style="float:left; margin-right:10px"}</a>
                    {/if}
                    {if $item->body}
                        <div class="bodycopy">
                            {$item->body}
                        </div>
                    {/if}
                    {clear}
                </div>
            {/foreach}
        {/foreach}
    {else}
        {foreach name=items from=$items item=item}
            <div class="item">
                <{$config.item_level|default:'h2'}><a class="li-link{if !empty($config.websnapr_key)} websnapr{/if}" {if $item->new_window}target="_blank"{/if} href="{$item->url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></{$config.item_level|default:'h2'}>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon action=delete record=$item}
                        {/if}
                    </div>
                {/permissions}
                {if $item->expFile[0]->id}
                    <a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{img file_id=$item->expFile[0]->id w=200 h=150 constrain=1 style="float:left; margin-right:10px"}</a>
                {/if}
                {if $item->body}
                    <div class="bodycopy">
                        {$item->body}
                    </div>
                {/if}
                {clear}
            </div>
        {/foreach}
    {/if}
</div>

{if !empty($config.websnapr_key)}
    {script unique=$name src="http://bubble.websnapr.com/`$config.websnapr_key`/swi/"}

    {/script}
{/if}
