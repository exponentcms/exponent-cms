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
 
{css unique="portfolio" link="`$asset_path`css/portfolio.css"}

{/css}

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module portfolio showall-simple-list">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add a Portfolio Piece"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                {/if}
            {/if}
			{*{if $permissions.manage == 1 && $rank == 1}*}
			{if $permissions.manage == 1 && $config.order == 'rank'}
				{ddrerank items=$page->records model="portfolio" label="Portfolio Pieces"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    {pagelinks paginate=$page top=1}
    {$cat="bad"}
    {foreach from=$page->records item=record}
        {if $cat !== $record->expCat[0]->id && $config.usecategories}
            <h2 class="category">{if $record->expCat[0]->title!= ""}{$record->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2>
        {/if}
        <div class="item">
        	<h3{if $config.usecategories} class="{$cat->color}"{/if}><a href="{link action=show title=$record->sef_url}" title="{$record->body|summarize:"html":"para"}">{$record->title}</a></h3>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {icon action=edit record=$record title="Edit `$record->title`"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete record=$record title="Delete `$record->title`"}
                    {/if}
                </div>
            {/permissions}
            {permissions}
                {if $permissions.create == 1}
                    {icon class="add" action=edit rank=$record->rank+1 title="Add another here"|gettext  text="Add a portfolio piece here"|gettext}
                {/if}
            {/permissions}
            {clear}
        </div>
        {$cat=$record->expCat[0]->id}
    {/foreach}
    {clear}
    {pagelinks paginate=$page bottom=1}
</div>
