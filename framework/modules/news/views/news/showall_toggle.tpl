{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="news-toggle"}
    .news.showall-toggle p.post-date {
        padding: 0!important;
        margin: 3px!important;
    }
{/css}
{if $config.show_collapsed}
    {$collapsed = true}
{elseif $config.show_collapsed_mobile && $smarty.const.MOBILE}
    {$collapsed = true}
{else}
    {$collapsed = false}
{/if}
<div class="module news showall showall-toggle">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class="add" action=edit rank=1 text="Add a news post"|gettext}
        {/if}
        {if $permissions.manage}
            {if !$config.disabletags}
            |  {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
            {/if}
            {*{if $rank == 1}*}
            {if $config.order == 'rank'}
            |  {ddrerank items=$page->records model="news" label="News Items"|gettext}
            {/if}
        {/if}
        {if $permissions.showUnpublished}
             |  {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    {foreach from=$items item=item name=items}
        <div class="item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
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
            {if $config.show_summary}
                {$summary = $item->body|summarize:"html":"parahtml"}
            {else}
                {$summary = ''}
            {/if}
            {toggle unique="text`$item->id`" title=$item->title|default:'Click to Hide/View'|gettext collapsed=$collapsed summary=$summary}
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
                </div>
                {if $config.datetag && !$config.hide_date}
                    <p class="post-date" style="float:right;">
                        <span class="month">{$item->publish_date|format_date:"%b"}</span>
                        <span class="day">{$item->publish_date|format_date:"%e"}</span>
                        <span class="year">{$item->publish_date|format_date:"%Y"}</span>
                    </p>
                {/if}
                {if !$config.datetag && !$config.hide_date}
                    <span class="date" style="float:right;">{$item->publish_date|format_date}</span>
                {/if}
                {if !$config.hide_tags}
                    {tags_assigned record=$item}
                {/if}
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
            {/toggle}
        </div>
    {/foreach}
</div>
