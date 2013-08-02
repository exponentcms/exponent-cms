{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="module news showall-recent">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{'Recent'|gettext} {$moduletitle}</h1>{/if}

    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            {icon class="add" action=edit rank=1 text="Add a news post"|gettext}
        {/if}
        {if $permissions.manage == 1}
            {if !$config.disabletags}
            |  {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
            {/if}
            {*{if $rank == 1}*}
            {if $config.order == 'rank'}
            |  {ddrerank items=$page->records model="news" label="News Items"|gettext}
            {/if}
        {/if}
        {if $permissions.showUnpublished == 1 }
            |  {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
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
                {$pp = ''}
            {/if}
            <h2>
                <a href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}" title="{$item->body|summarize:"html":"para"}">
                {$item->title}
                </a>
            </h2>
            {if !$config.datetag}
                <span class="date">{$item->publish_date|format_date}</span>
                {$pp = '&#160;&#160;|&#160;&#160;'}
            {/if}
            {tags_assigned record=$item prepend=$pp}
            {if $item->isRss != true}
                {permissions}
                <div class="item-actions">
                    {if $permissions.edit == true}
                        {if $myloc != $item->location_data}
                            {if $permissions.manage == 1}
                                {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                            {else}
                                {icon img='arrow_merge.png' title="Merged Content"|gettext}
                            {/if}
                        {/if}
                        {icon action=edit record=$item}
                    {/if}
                    {if $permissions.delete == true}
                        {icon action=delete record=$item}
                    {/if}
                </div>
                {/permissions}
            {/if}
            <div class="bodycopy">
                {if $config.ffloat != "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                {if $config.usebody==1}
                    <p>{$item->body|summarize:"html":"paralinks"}</p>
                {elseif $config.usebody==2}
				{else}
                    {$item->body}
                {/if}
                {if $config.ffloat == "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                <a class="readmore" href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}">{"Read More"|gettext}</a>
            </div>
            {if $config.enable_tweet}
                <a href="https://twitter.com/share" class="twitter-share-button" data-url="{$smarty.const.URL_FULL}{$item->sef_url}" data-text="{$item->title}"{if $config.layout} data-count="{$config.layout}"{/if}{if $config.size} data-size="{$config.size}"{/if} data-lang="en">{'Tweet'|gettext}</a>
                {script unique='tweet_src'}
                {literal}
                    !function(d,s,id){
                        var js,fjs=d.getElementsByTagName(s)[0];
                        if(!d.getElementById(id)){
                            js=d.createElement(s);
                            js.id=id;
                            js.src="https://platform.twitter.com/widgets.js";
                            fjs.parentNode.insertBefore(js,fjs);
                        }
                    }(document,"script","twitter-wjs");
                {/literal}
                {/script}
            {/if}
            {clear}
        </div>
        {/if}
    {/foreach}
    {if $page->total_records > $config.headcount}
        {*{icon action="showall" text="More Items in"|gettext|cat:' '|cat:$moduletitle|cat:' ...'}*}
        {pagelinks paginate=$page more=1 text="More Items in"|gettext|cat:' '|cat:$moduletitle|cat:' ...'}
    {/if}
</div>
