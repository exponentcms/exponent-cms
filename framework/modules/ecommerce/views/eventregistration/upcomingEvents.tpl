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
{css unique="event-listings" link="`$asset_path`css/storefront.css" corecss="common"}

{/css}

{css unique="event-listings1" link="`$asset_path`css/eventregistration.css"}

{/css}

<div class="module events showall headlines">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
            {/if}
            {if $permissions.manage}
                 {icon action=manage text="Manage Events"|gettext}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <ul>
        {foreach name=items from=$page->records item=item}
            {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
                <li>
                    <div class="events">
                        <a class="link" href="{link action=show title=$item->sef_url}">{$item->title}</a>
                        {br}<em class="date">{$item->eventdate|format_date}</em>{br}
                        {$item->body|summarize:"text":"paralinks"}<span>
                        {if $item->getBasePrice()}{br}{'Cost'|gettext}: {$item->getBasePrice()|currency}{/if}
                        {if $item->isRss != true}
                            {permissions}
                                <div class="item-actions">
                                    {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                        {icon controller="store" action=edit record=$item}
                                        {icon controller="store" action=copyProduct class="copy" record=$item text="Copy" title="Copy `$item->title` "}
                                    {/if}
                                    {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                        {icon controller="store" action=delete record=$item}
                                    {/if}
                                </div>
                            {/permissions}
                        {/if}
                    </div>
                </li>
            {/if}
        {/foreach}
    </ul>
</div>