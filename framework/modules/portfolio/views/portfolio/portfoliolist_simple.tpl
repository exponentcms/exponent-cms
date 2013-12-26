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
                    {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                        {if $myloc != $record->location_data}
                            {if $permissions.manage}
                                {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                            {else}
                                {icon img='arrow_merge.png' title="Merged Content"|gettext}
                            {/if}
                        {/if}
                        {icon action=edit record=$record title="Edit `$record->title`"}
                        {icon action=copy record=$record title="Copy `$record->title`"}
                    {/if}
                    {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                        {icon action=delete record=$record title="Delete `$record->title`"}
                    {/if}
                </div>
            {/permissions}
            {permissions}
                {if $permissions.create}
                    {icon class="add" action=edit rank=$record->rank+1 title="Add another here"|gettext  text="Add a portfolio piece here"|gettext}
                {/if}
            {/permissions}
            {clear}
        </div>
        {$cat=$record->expCat[0]->id}
    {/foreach}
    {clear}
    {pagelinks paginate=$page bottom=1}
