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

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}

<div class="module blog showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{/if}
    {if $config.enable_rss == true}
        <a class="rsslink" href="{rsslink}" title="{'Subscribe to'|gettext} {$config.feed_title}"></a>
    {/if}
    {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}</h1>{/if}
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
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var=myloc value=serialize($__loc)}
    {pagelinks paginate=$page top=1}
    {foreach from=$page->records item=item}
        <div class="item">
            <h2>
            <a href="{link action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">
            {$item->title}
            </a>
            </h2>
            <div class="post-info">
                <span class="attribution">
                    {if $item->private}<strong>({'Draft'|gettext})</strong>{/if}
                    {if $item->publish_date > $smarty.now}
                        <strong>{'Will be'|gettext}&nbsp;
                    {/if}
                    {'Posted by'|gettext} <a href="{link action=showall_by_author author=$item->poster|username}">{attribution user_id=$item->poster}</a> {'on'|gettext} <span class="date">{$item->publish_date|format_date}</span>
                    {if $item->publish_date > $smarty.now}
                        </strong>&nbsp;
                    {/if}
                </span>

                | <a class="comments" href="{link action=show title=$item->sef_url}#exp-comments">{$item->expComment|@count} {"Comments"|gettext}</a>
                
				{if $item->expTag|@count>0 && !$config.disabletags}
				| <span class="tags">
					{"Tags"|gettext}: 
					{foreach from=$item->expTag item=tag name=tags}
					<a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
					{/foreach} 
				</span>
				{/if}
            </div>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {if $myloc != $item->location_data}
                            {if $permissions.manage == 1}
                                {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                            {else}
                                {icon img='arrow_merge.png' title="Merged Content"|gettext}
                            {/if}
                        {/if}
                        {icon action=edit record=$item}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete record=$item}
                    {/if}
                </div>
            {/permissions}
            <div class="bodycopy">
                {if $config.filedisplay != "Downloadable Files"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
    			{if $config.usebody==1}
    				<p>{$item->body|summarize:"html":"paralinks"}</p>
    			{elseif $config.usebody==2}
    			{else}
    				{$item->body}
    			{/if}			
                {if $config.filedisplay == "Downloadable Files"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
            </div>
        </div>
    {/foreach}    
    {pagelinks paginate=$page bottom=1}
</div>
