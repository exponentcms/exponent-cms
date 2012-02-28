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

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

{css unique="links" link="`$asset_path`css/links.css"}

{/css}

<div class="module links showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1 || $permissions.edit == 1}
				{icon class=add action=create text="Add a new link"|gettext}
			{/if}
			{if $permissions.manage == 1 && $rank == 1}
				{ddrerank items=$items model="links" label="Links"|gettext}
			{/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var=myloc value=serialize($__loc)}
    {if $config.usecategories}
        {foreach from=$cats key=catid item=cat}
            {if $catid != 0}
                <h2 class="category">{$cat->name}</h2>
            {/if}
            {foreach name=items from=$cat->records item=item}
                <div class="item">
                    <h3 class="link-title"><a {if $item->new_window}target="_blank"{/if} href="{$item->url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></h3>
                    {permissions}
                        <div class="item-actions">
                            {if $myloc != $item->location_data}{icon class=merge img='arrow_merge.png' title="Aggregated Content"|gettext}{/if}
                            {if $permissions.edit == 1}
                                {icon action=edit record=$item}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon action=delete record=$item}
                            {/if}
                        </div>
                    {/permissions}
                    {if $item->expFile[0]->id}
                        <a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{img file_id=$item->expFile[0]->id width=200 height=150 constrain=1 style="float:left; margin-right:10px"}</a>
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
                <h2><a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{$item->title}</a></h2>
                {permissions}
                    <div class="item-actions">
                        {if $myloc != $item->location_data}{icon class=merge img='arrow_merge.png' title="Aggregated Content"|gettext}{/if}
                        {if $permissions.edit == 1}
                            {icon action=edit record=$item}
                        {/if}
                        {if $permissions.delete == 1}
                            {icon action=delete record=$item}
                        {/if}
                    </div>
                {/permissions}
                {if $item->expFile[0]->id}
                    <a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{img file_id=$item->expFile[0]->id width=200 height=150 constrain=1 style="float:left; margin-right:10px"}</a>
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
