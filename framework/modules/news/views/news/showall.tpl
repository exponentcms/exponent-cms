{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

<div class="module news showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{/if}
    {if $config.enable_rss == true}
        <a class="rsslink" href="{rsslink}" title="{'Subscribe to'|gettext} {$config.feed_title}"></a>
    {/if}
    {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}</h1>{/if}

    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            {icon class="add" action=create text="Add a news post"|gettext}</a>
        {/if}
        {if $permissions.showUnpublished == 1 }
             |  {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}</a>
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}

    {pagelinks paginate=$page top=1}
    {foreach from=$page->records item=item}
        <div class="item">
            <h2>
                <a href="{if $item->isRss}{$item->rss_link}{else}{link action=showByTitle title=$item->sef_url}{/if}">
                {$item->title}
                </a>
            </h2>
            {if $item->isRss != true}
                {permissions}
                <div class="item-actions">
                    {if $permissions.edit == true}
                        {icon action=edit record=$item}
                    {/if}
                    {if $permissions.delete == true}
                        {icon action=delete record=$item}
                    {/if}
                </div>
                {/permissions}
            {/if}
            <span class="date">{$item->publish_date|date_format}</span>
            {if $item->expTag[0]->id}
                | <span class="tags">
                    {"Tags"|gettext}:
                    {foreach from=$item->expTag item=tag name=tags}
                        <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
                    {/foreach}
                </span>
            {/if}

            <div class="bodycopy">
                {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}

                {if $config.usebody==1}
                    <p>{$item->body|summarize:"html":"paralinks"}</p>
                {elseif $config.usebody==2}
				{else}
                    {$item->body}
                {/if}

                <a class="readmore" href="{if $item->isRss}{$item->rss_link}{else}{link action=showByTitle title=$item->sef_url}{/if}">{"Read More"|gettext}</a>
            </div>
            {clear}
        </div>
    {/foreach}
    {pagelinks paginate=$page bottom=1}
</div>
