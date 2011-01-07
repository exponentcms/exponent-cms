{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

    {$page->links}
    {foreach from=$page->records item=item}
        <div class="item">
            <h2>
                <a href="{if $item->isRss}{$item->rss_link}{else}{link action=showByTitle title=$item->sef_url}{/if}">
                {$item->title}
                </a>
            </h2>
            {if $item->isRss != true}
                {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
                <div class="itemactions">
                    {if $permissions.edit == true}
                        {icon controller=news action=edit id=$item->id title="Edit this news post"}
                    {/if}
                    {if $permissions.delete == true}
                        {icon controller=news action=delete id=$item->id title="Delete this news post" onclick="return confirm('Are you sure you want to delete `$item->title`?');"}
                    {/if}
                    {if $permissions.edit == true && $config.order == 'rank ASC'}
                        {if $smarty.foreach.items.first == 0}
                            {icon controller=news action=rerank img=up.png id=$item->id push=up}    
                        {/if}
                        {if $smarty.foreach.items.last == 0}
                            {icon controller=news action=rerank img=down.png id=$item->id push=down}
                        {/if}
                    {/if}
                </div>
                {/permissions}
            {/if}
            <span class="date">{$item->publish_date|date_format}</span>

            <div class="bodycopy">
                {if $item->expFile[0]->id}
                    {img class="thumbnail" file_id=$item->expFile[0]->id w=200 alt=$item->expFile[0]->alt}
                {/if}
                {if $config.truncate}
                    <p>{$item->body|summarize:"html":"para"}</p>
                {else}
                    {$item->body}
                {/if}
                <a class="readmore" href="{if $item->isRss}{$item->rss_link}{else}{link action=showByTitle title=$item->sef_url}{/if}">{"Read More"|gettext}</a>
            </div>
            <div style="clear:both"></div>
        </div>
    {/foreach}
    {$page->links}
    
    {permissions level=$smarty.const.UILEVEL_NORMAL}
    {if $morenews == 1 || $permissions.create == true || $permissions.edit == true || $permissions.showExpired == 1}
    <div class="moduleactions">
        {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.create == true || $permissions.edit == true}
            <a class="addnews" href="{link action=create}">{$_TR.create_news|default:"Create a news post"}</a>{br}
        {/if}
        {if $permissions.showUnpublished == 1 }
            <a class="expirednews" href="{link action=showUnpublished}">{$_TR.view_expired|default:"View Expired/Unpublished News"}</a>
        {/if}
        {/permissions}
    </div>
    {/if}
    {/permissions}
</div>
