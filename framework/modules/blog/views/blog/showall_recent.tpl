{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}

<div class="module blog showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{/if}
    {if $config.enable_rss == true}
        <a class="rsslink" href="{rsslink}" title="{'Subscribe to'|gettext} {$config.feed_title}"></a>
    {/if}
    {if $moduletitle && !$config.hidemoduletitle}{'Recent Posts from'|gettext} '{$moduletitle}'</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit text="Add a new blog article"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {icon controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
		</div>
    {/permissions}
    {foreach name=items from=$page->records item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
        <div class="item">
            <h2>
            <a href="{link action=show title=$item->sef_url}">
            {$item->title}
            </a>
            </h2>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {icon action=edit record=$item}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete record=$item}
                    {/if}
                </div>
            {/permissions}
            <div class="post-info">
                <span class="attribution">
                    {'Posted by'|gettext} <a href="{link action=showall_by_author author=$item->poster|username}">{attribution user_id=$item->poster}</a> {'on'|gettext} <span class="date">{$item->created_at|format_date}</span>
                </span>

                | <a class="comments" href="{link action=show title=$item->sef_url}#exp-comments">{$item->expComment|@count} {"Comments"|gettext}</a>
                
				{if $item->expTag[0]->id}
				| <span class="tags">
					{"Tags"|gettext}: 
					{foreach from=$item->expTag item=tag name=tags}
					<a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
					{/foreach} 
				</span>
				{/if}
            </div>
            <div class="bodycopy">
                {filedisplayer view="`$config.filedisplay`" files=$item->expFile item=$item is_listing=1}
    			{if $config.usebody==1}
    				<p>{$item->body|summarize:"html":"paralinks"}</p>
    			{elseif $config.usebody==2}
    			{else}
    				{$item->body}
    			{/if}			
                
            </div>
        </div>
        {/if}
    {/foreach}    
    {if $page->total_records > $config.headcount}
        {icon action="showall" text="More Posts in '`$moduletitle`' ..."|gettext}
    {/if}
</div>
