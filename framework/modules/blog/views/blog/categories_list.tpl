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

<div class="module blog categories_list">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
        {if $permissions.manage}
            {if $config.use_categories}
                {icon controller=expCat class="manage" action=manage_module model='blog' text="Manage Categories"|gettext}
            {/if}
        {/if}
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <ul>
        {foreach from=$cats item=cat}
            <li>
                <a href="{link action=showall cat=$cat->id}" title='{"View all posts filed under"|gettext} {$cat->title}'>{$cat->title} ({$cat->count})</a>
            </li>
        {/foreach}
    </ul>
</div>
