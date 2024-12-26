{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div class="module portfolio tags-list">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit text="Add a Portfolio Piece"|gettext}
			{/if}
			{if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                {/if}
                {*{if $slides|@count>1 && $rank == 1}*}
                {if $slides|@count>1 && $config.order == 'rank'}
				    {ddrerank items=$slides model="portfolio" label="Portfolio Pieces"|gettext}
                {/if}
			{/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <ul>
        {foreach from=$tags item=tag}
            <li>
                <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title} ({$tag->count})</a>
            </li>
        {/foreach}
    </ul>
</div>
