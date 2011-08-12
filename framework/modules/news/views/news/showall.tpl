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
    {if $enable_rss == true}
        <a class="rsslink" href="{rsslink}">Subscribe to {$config.feed_title}</a>
    {/if}
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}

    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            <a class="add" href="{link action=create}">{"Add a news post"|gettext}</a>
        {/if}
        {if $permissions.showUnpublished == 1 }
              |  <a class="view" href="{link action=showUnpublished}">{"View Expired/Unpublished News"|gettext}</a>
        {/if}
    </div>
    {/permissions}

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
                        {icon action=edit record=$item title="Edit News Post"}
                    {/if}
                    {if $permissions.delete == true}
                        {icon action=delete record=$item title="Delete News Post" onclick="return confirm('Are you sure you want to delete `$item->title`?');"}
                    {/if}
                </div>
                {/permissions}
            {/if}
            <span class="date">{$item->publish_date|date_format}</span>

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
            <div style="clear:both"></div>
        </div>
    {/foreach}
    {pagelinks paginate=$page bottom=1}
</div>
