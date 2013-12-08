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
 
{css unique="news" link="`$asset_path`css/news.css"}

{/css}
 
<div class="module news headlines">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class="add" action=edit rank=1 title="Add a news post"|gettext}
			{/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                   {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
                {/if}
                {*{if $rank == 1}*}
                {if $config.order == 'rank'}
                   {ddrerank items=$page->records model="news" label="News Items"|gettext}
                {/if}
            {/if}
			{if $permissions.showUnpublished }
				{icon class="view" action=showUnpublished text="View Unpublished"|gettext}
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
            <li class="item">
                <a class="link" href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}" title="{$item->body|summarize:"html":"para"}">
                    {$item->title}
                </a>

                {if !$config.hidedate}
                    <em class="date">{$item->publish_date|format_date}</em>
                {/if}

                {if $item->isRss != true}
                    {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon action=delete record=$item}
                        {/if}
                    </div>
                    {/permissions}
                {/if}
            </li>
        {/if}
    {/foreach}
    </ul>
    {if $page->total_records > $config.headcount}
        {*{icon action="showall" text="More News..."|gettext}*}
        {pagelinks paginate=$page more=1 text="More News..."|gettext}
    {/if}
</div>
