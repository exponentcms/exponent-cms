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
{css unique="event-listings" link="`$asset_path`css/storefront.css" corecss="button,tables"}
{literal}
	.headlines .events {
		overflow: hidden;
	}
{/literal}
{/css}

<div class="module events showall headlines">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create == true || $permissions.edit == true}
                {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
            {/if}
            {if $permissions.manage == 1}
                 {icon action=manage text="Manage Events"|gettext}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var=myloc value=serialize($__loc)}
    <ul>
        {foreach name=items from=$page->records item=item}
            {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
                <li>
                    <div class="events">
                        <a class="link" href="{link action=showByTitle title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                        <a href="{link action=showByTitle title=$item->sef_url}"></a>
                        - <em class="date">{$item->eventdate|date_format}</em>
                        {if $item->isRss != true}
                            {permissions}
                            <div class="item-actions">
                                {if $permissions.edit == true}
                                    {icon controller="store" action=edit record=$item}
                                {/if}
                                {if $permissions.delete == true}
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