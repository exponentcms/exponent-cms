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

<div class="module store upcoming-events">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
        {/if}
        {if $permissions.manage == 1}
             {icon controller=eventregistration action=manage text="Manage Events"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <ul>
        {$limit = 5}
        {if (!empty($config.event_limit))}{$limit = $config.event_limit}{/if}
        {foreach name=uce from=$page->records item=item}
            {if $smarty.foreach.uce.iteration <= $limit}
            <li>
                <a href="{link controller=eventregistration action=show title=$item->sef_url}">{$item->eventdate|format_date:"%A, %B %e, %Y"}</a>
                {*<p>{$item->summary|truncate:75:"..."}</p>*}
                <p>{$item->title}</p>
            </li>
        {/if}
        {/foreach}
    </ul>
</div>
