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

<div class="module blog tags_list">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        {if $permissions.manage}
            {if !$config.disabletags}
                {icon controller=expTag class="manage" action=manage_module model='blog' text="Manage Tags"|gettext}
            {/if}
        {/if}
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <ul>
        {foreach from=$tags item=tag}
            <li>
                <a href="{link action=showall_by_tags tag=$tag->sef_url}" title="{'View all posts tagged with'|gettext} '{$tag->title}'">{$tag->title} ({$tag->count})</a>
            </li>
        {/foreach}
    </ul>
</div>
