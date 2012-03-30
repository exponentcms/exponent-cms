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

<div class="module filedownload showall-by-tag">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {icon controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div class="bodycopy">
    <ul>
        {foreach from=$tags item=tag}
            <li>
                <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title} ({$tag->count})</a>
            </li>
        {/foreach}
    </ul>
    </div>
</div>
