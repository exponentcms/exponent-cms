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
    {rss_link}
    {if $moduletitle && !$config.hidemoduletitle}{'Recent Posts from'|gettext} '{$moduletitle}'</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit text="Add a new blog article"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='blog' text="Manage Tags"|gettext}
                {/if}
            {/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    {*{assign var=myloc value=serialize($__loc)}*}
    {$myloc=serialize($__loc)}
    {foreach name=items from=$page->records item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
        <div class="item">
            {if $config.datetag}
                <p class="post-date">
                    <span class="month">{$item->publish_date|format_date:"%b"}</span>
                    <span class="day">{$item->publish_date|format_date:"%e"}</span>
                    <span class="year">{$item->publish_date|format_date:"%Y"}</span>
                </p>
            {/if}
            <h2>
            <a href="{link action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">
            {$item->title}
            </a>
            </h2>
            <div class="post-info">
                <span class="attribution">
                    {if $item->private}<strong>({'Draft'|gettext})</strong>{/if}
                    {if $item->publish_date > $smarty.now}
                        <strong>{'Will be'|gettext}&#160;
                    {/if}
                    {if !$config.displayauthor}
                        <span class="label posted">{'Posted by'|gettext}</span>
                        <a href="{link action=showall_by_author author=$item->poster|username}">{attribution user_id=$item->poster}</a>
                    {/if}
                    {if !$config.datetag}
                        {'on'|gettext} <span class="date">{$item->publish_date|format_date}</span>
                    {/if}
                    {if $item->publish_date > $smarty.now}
                        </strong>&#160;
                    {/if}
                </span>
                {comments_count item=$record prepend='&#160;&#160;|&#160;&#160;'}
                {tags_assigned item=$record prepend='&#160;&#160;|&#160;&#160;'}
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
            {if $config.usebody!=2}
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
            {/if}
        </div>
        {/if}
    {/foreach}    
    {if $page->total_records > $config.headcount}
        {*{icon action="showall" text="More Items in"|gettext|cat:' '|cat:$moduletitle|cat:' ...'}*}
        {pagelinks paginate=$page more=1 text="More Items in"|gettext|cat:' '|cat:$moduletitle|cat:' ...'}
    {/if}
</div>
