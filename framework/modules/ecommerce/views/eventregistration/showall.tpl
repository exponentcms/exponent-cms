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

{/css}

{css unique="event-listings1" link="`$asset_path`css/eventregistration.css"}

{/css}

<div class="module events showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
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
    {$myloc=serialize($__loc)}
    <ul>
        {foreach name=items from=$page->records item=item}
            {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
                <li>
                    <h3><a class="link" href="{link action=showByTitle title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">
                        {$item->title}
                    </a></h3>
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
                    <div class="events">
                        <div class="event-image">
                             <a href="{link action=showByTitle title=$item->sef_url}">
                            {img file_id=$item->expFile.mainimage[0]->id w=125 alt=$item->image_alt_tag|default:"Image of `$item->title`" title="`$item->title`"}
                            </a>
                        </div>

                        <div class="event-info">
                            <em class="date">{$item->eventdate|date_format:"%A, %B %e, %Y"}</em>
                            <p>{$item->body|truncate:175:"..."}</p>
                            {*<a href="{link action=showByTitle title=$item->sef_url}" class="readmore">{'Read More...'|gettext}</a>*}

                        </div>
                    </div>
                </li>
            {/if}
        {/foreach}
    </ul>
</div>