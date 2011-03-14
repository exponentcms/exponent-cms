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
<div class="module news showall-summary">
    {if $enable_rss == true}
        <a class="rsslink" href="{rsslink}">Subscribe to {$config.feed_title}</a>
    {/if}
    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    
    {$page->links}
    {foreach key="key" name="items" from=$page->records item=item}
        <div class="item">          
            {if $item->title != ""}<h2>{$item->title}</h2>{/if}
            {if $item->isRss != true}
                {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
                <div class="item-actions">
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
            <span class="date">{$item->publish|format_date:"%A, %B %e, %Y"}</span>
            <div class="bodycopy">
                {$item->body|summarize:"html":"para"}
                <a class="readmore" href="{if $item->isRss}{$item->rss_link}{else}{link action=showByTitle title=$item->sef_url}{/if}">{$_TR.read_more|default:"Read More"}</a>
            </div>          
        </div>
    {/foreach}
    {$page->links}
    
    {permissions}
    {if $morenews == 1 || $permissions.create == true || $permissions.edit == true || $permissions.showExpired == 1}
    <div class="module-actions">
        {permissions}
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


