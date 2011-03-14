{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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
        <a class="rsslink" href="{rsslink}">Subscribe to {$config.feed_title}</a>
    {/if}
    {if $moduletitle != ""}<h2>{$moduletitle}</h2>{/if}

    {permissions}
        <div class="module-actions">
        {if $permissions.create == true || $permissions.edit}
            <a class="add" href="{link action=create}">{"Create a news post"|gettext}</a>
        {/if}
        
        {if $permissions.showUnpublished == 1 }
            <a class="view" href="{link action=showUnpublished}">{"View Unpublished"|gettext}</a>
        {/if}
        </div>
    {/permissions}

    
    <ul>
    {foreach name=items from=$page->records item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
            
        <li>
            <a class="link" href="{if $item->isRss}{$item->rss_link}{else}{link action=showByTitle title=$item->sef_url}{/if}">                 
                {$item->title}
            </a>
            
            {if !$config.hidedate}
                <span class="date">{$item->publish_date|format_date}</span>
            {/if}
            
            {if $item->isRss != true}
                {permissions}
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
        </li>

        {/if}
    {/foreach}
    </ul>    
</div>
