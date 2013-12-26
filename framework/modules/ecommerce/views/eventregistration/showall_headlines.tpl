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

<div class="module store upcoming-events">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
        {/if}
        {if $permissions.manage}
             {icon controller=eventregistration action=manage text="Manage Events"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <ul>
        {foreach name=uce from=$page->records item=item}
            {if $smarty.foreach.uce.iteration<=$config.headcount || !$config.headcount}
                <div class="vevent">
                <li>
                    <a class="url{if $item->eventdate < time()} date past{/if}" href="{link controller=eventregistration action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}"><span class="dtstart">{$item->eventdate|format_date:"%A, %B %e, %Y"}<span class="value-title" title="{date('c',$item->eventdate)}"></span></span></a>
                    {*<p>{$item->summary|truncate:75:"..."}</p>*}
                    <span class="hide">
                        <span class="location">
                        {if !empty($item->location)}
                            {$item->location}
                        {else}
                            {$smarty.const.ORGANIZATION_NAME}
                        {/if}
                        </span>
                    </span>
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
                    <p>
                        <span class="summary">{$item->title}</span>
                        <span class="tickets">
                          <span class="hoffer">
                          <span class="currency hide">{$smarty.const.ECOM_CURRENCY}</span>
                          {if $item->getBasePrice()}- {'Cost'|gettext}: <span class="price">{$item->getBasePrice()|currency}</span>{/if}
                          <span class="quantity hide">{$item->spacesLeft()}</span>
                          </span>
                        </span>
                    </p>
                </li>
                </div>
            {/if}
        {/foreach}
    </ul>
</div>
