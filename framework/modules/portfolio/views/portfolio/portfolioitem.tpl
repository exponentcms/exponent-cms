{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="item">
	<h3{if $config.usecategories} class="{$cat->color}"{/if}><a href="{link action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></h3>
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
				{icon action=edit record=$item title="Edit `$item->title`"}
                {icon action=copy record=$item title="Copy `$item->title`"}
			{/if}
			{if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
				{icon action=delete record=$item title="Delete `$item->title`"}
			{/if}
		</div>
	{/permissions}
    {tags_assigned record=$item}
    <div class="bodycopy">
        {if $config.ffloat != "Below"}
            {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
        {/if}
        {$link = '<a href="'|cat:makeLink([controller=>portfolio, action=>show, title=>$item->sef_url])|cat:'"><em>'|cat:'(read more)'|gettext|cat:'</em></a>'}
        {if $config.usebody==1}
            {*<p>{$item->body|summarize:"html":"paralinks"}</p>*}
            <p>{$item->body|summarize:"html":"parahtml":$link}</p>
        {elseif $config.usebody==3}
            {$item->body|summarize:"html":"parapaged":$link}
        {elseif $config.usebody==2}
        {else}
            {$item->body}
        {/if}
        {if $config.ffloat == "Below"}
            {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
        {/if}
    </div>
    {clear}
    {permissions}
        {if $permissions.create}
            <div class="module-actions">
                {icon class="add" action=edit rank=$item->rank+1 title="Add another here"|gettext  text="Add a portfolio piece here"|gettext}
            </div>
        {/if}
    {/permissions}
</div>