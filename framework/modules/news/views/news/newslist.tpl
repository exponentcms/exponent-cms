{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

    {$myloc=serialize($__loc)}
    {pagelinks paginate=$page top=1}
    {foreach from=$page->records item=item}
        <div class="item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
            {if $config.datetag}
                <p class="post-date">
                    <span class="month">{$item->publish_date|format_date:"%b"}</span>
                    <span class="day">{$item->publish_date|format_date:"%e"}</span>
                    <span class="year">{$item->publish_date|format_date:"%Y"}</span>
                </p>
            {$pp = ''}
            {/if}
            <{$config.heading_item|default:'h2'}>
                <a href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}" title="{$item->body|summarize:"html":"para"}">
                {$item->title}
                </a>
            </{$config.heading_item|default:'h2'}>
            {if !$config.datetag}
                <span class="date">{$item->publish_date|format_date}</span>
            {$pp = '&#160;&#160;|&#160;&#160;'|not_bs}
            {/if}
            {tags_assigned record=$item prepend=$pp}
            {if $item->isRss != true}
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $item->revision_id > 1 && $smarty.const.ENABLE_WORKFLOW}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$item->revision_id}">{$item->revision_id}</span>{/if}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item}
                            {icon action=copy record=$item}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon action=delete record=$item}
                        {/if}
                        {if !$item->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $item->poster == $user->id))}
                            {icon action=approve record=$item}
                        {/if}
                    </div>
                {/permissions}
            {/if}
            <div class="bodycopy">
                {if $config.ffloat != "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                {$link = '<a href="'|cat:makeLink([controller=>news, action=>show, title=>$item->sef_url])|cat:'"><em>'|cat:'(read more)'|gettext|cat:'</em></a>'}
                {if $config.usebody==1}
                    {*<p>{$item->body|summarize:"html":"paralinks"}</p>*}
                    <p>{$item->body|summarize:"html":"parahtml":$link}</p>
                {elseif $config.usebody==3}
                    {$item->body|summarize:"html":"parapaged":$link}
                {elseif $config.usebody==2}
				{else}
                    {$item->body}
                {/if}
                {if $config.ffloat == "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                {*<a class="readmore" href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}">{"Read More"|gettext}</a>*}
            </div>
            {if $config.enable_tweet}
                <a href="https://twitter.com/share" class="twitter-share-button" data-url="{link action=show title=$item->sef_url}" data-text="{$item->title}"{if $config.layout} data-count="{$config.layout}"{/if}{if $config.size} data-size="{$config.size}"{/if} data-lang="en">{'Tweet'|gettext}</a>
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
    {/foreach}
    {pagelinks paginate=$page bottom=1}
