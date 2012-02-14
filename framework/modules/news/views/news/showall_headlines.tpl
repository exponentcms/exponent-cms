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
 
{css unique="news" link="`$asset_path`css/news.css"}

{/css}
 
<div class="module news headlines">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{/if}
    {if $config.enable_rss == true}
        <a class="rsslink" href="{rsslink}" title="{'Subscribe to'|gettext} {$config.feed_title}"></a>
    {/if}
    {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}</h2>{/if}

    {permissions}
        <div class="module-actions">
			{if $permissions.create == true || $permissions.edit}
				{icon class="add" action=create title="Add a news post"|gettext}</a>
			{/if}
			{if $permissions.showUnpublished == 1 }
				{icon class="view" action=showUnpublished title="View Unpublished"|gettext}</a>
			{/if}
        </div>
    {/permissions}

    <ul>
    {foreach name=items from=$page->records item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}

        <li>
            <a class="link" href="{if $item->isRss}{$item->rss_link}{else}{link action=showByTitle title=$item->sef_url}{/if}" title="{$item->body|summarize:"html":"para"}">
                {$item->title}
            </a>
            
            {if !$config.hidedate}
                <em class="date">{$item->publish_date|format_date}</em>
            {/if}
            
            {if $item->isRss != true}
                {permissions}
                <div class="item-actions">
                     {if $permissions.edit == true}
                        {icon action=edit record=$item}
                    {/if}
                    {if $permissions.delete == true}
                        {icon action=delete record=$item}
                    {/if}
                    {if $permissions.edit == true && $config.order == 'rank ASC'}
                        {if $smarty.foreach.items.first == 0}
                            {icon action=rerank img='up.png' record=$item push=up}
                        {/if}
                        {if $smarty.foreach.items.last == 0}
                            {icon action=rerank img='down.png' record=$item push=down}
                        {/if}
                    {/if}
                </div>
                {/permissions}
            {/if}
        </li>

        {/if}
    {/foreach}
    </ul>
    {if $page->total_records > $config.headcount}
        {icon action="showall" text="More News..."|gettext}
    {/if}
</div>
