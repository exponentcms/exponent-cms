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
{css unique="event-listings" link="`$asset_path`css/storefront.css" corecss="common"}

{/css}

{css unique="event-listings1" link="`$asset_path`css/eventregistration.css"}

{/css}

<div class="module events showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
            {/if}
            {if $permissions.manage}
                 {icon action=manage text="Manage Active Events"|gettext}
            {/if}
            {if $admin}
                {if !$past}
                    {icon class="view" action=showall past=1 text="View Past Events"|gettext}
                {else}
                    {icon class="view" action=showall text="View Active Events"|gettext}
                {/if}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <ul>
        {foreach name=items from=$page->records item=item}
            {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
                <div class="vevent">
                <li>
                    <h3><a class="url link" href="{link action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">
                        <span class="summary">{$item->title}</span>
                    </a></h3>
                    <span class="hide">
                        <span class="location">
                        {if !empty($item->location)}
                            {$item->location}
                        {else}
                            {$smarty.const.ORGANIZATION_NAME}
                        {/if}
                        </span>
                    </span>
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
                    <div class="events">
                        <div class="event-image">
                            <a href="{link action=show title=$item->sef_url}">
                                {if $item->expFile.mainimage[0]->id != ""}
                                    {img file_id=$item->expFile.mainimage[0]->id w=125 alt=$item->image_alt_tag|default:"Image of `$item->title`" title="`$item->title`" class="photo"}
                                {else}
                                    {img src="`$asset_path`images/no-image.jpg" w=125 alt=$item->image_alt_tag|default:"Image of `$item->title`" title="`$item->title`"}
                                {/if}
                            </a>
                        </div>
                        <div class="event-info">
                            <em class="date{if $item->eventdate < time()} past{/if}"><span class="dtstart">{$item->eventdate|format_date:"%A, %B %e, %Y"}<span class="value-title" title="{date('c',$item->eventdate)}"></span></span></em>
                            <span class="tickets">
                              <span class="hoffer">
                              <span class="currency hide">{$smarty.const.ECOM_CURRENCY}</span>
                                  {if $item->getBasePrice()}<p>{'Cost'|gettext}: <span class="price">{$item->getBasePrice()|currency}</span></p>{/if}
                                  <span class="quantity hide">{$item->spacesLeft()}</span>
                              </span>
                            </span>
                            <span class="description">{$item->body|truncate:175:"..."}</span>
                            {*<a href="{link action=show title=$item->sef_url}" class="readmore">{'Read More...'|gettext}</a>*}
                        </div>
                    </div>
                </li>
                </div>
            {/if}
        {/foreach}
    </ul>
</div>