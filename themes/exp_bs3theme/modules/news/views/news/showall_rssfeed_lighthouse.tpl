{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
    {if $enable_rss == true}
        <a class="rsslink" href="{rsslink}">{'Subscribe to'|gettext} {$config.feed_title}</a>
    {/if}
    {if $moduletitle != ""}<h2>{$moduletitle}</h2>{/if}

    {permissions}
        <div class="module-actions">
			{if $permissions.create == true || $permissions.edit}
                {icon class="add" action=edit rank=1 text="Add a news post"|gettext}
			{/if}
			{if $permissions.showUnpublished == 1 }
                {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}
			{/if}
        </div>
    {/permissions}

    <ul>
    {foreach name=items from=$page->records item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}

        <li>
			<span class="date">{$item->publish_date|date_format:"%b %d, %Y @ %I:%M %p"} by {$item->poster}</span>
			{*<a href="{$item->rss_link}" target="_blank">{$item->forum} | {$item->topic}</a>*}
            <a href="{$item->rss_link}" target="_blank">{$item->forum}</a>
            {*<a href="{$item->rss_link}" target="_blank">{$item->body}</a>*}

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
    <p>
        <a href="http://exponentcms.lighthouseapp.com" target="_blank" title="The place for Exponent CMS Project Issue Tracking" class="biglink">More on Lighthouse</a>
    </p>
</div>
