{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

{if !empty($config.enable_facebook_like) || !empty($config.displayfbcomments)}
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId={$config.app_id}&autoLogAppEvents=1" nonce="9wKafjYh"></script>
{/if}

{if $config.enable_tweet}
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
{/if}

<div class="module news showall showall-recent">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{'Recent'|gettext} {$moduletitle}</{$config.heading_level|default:'h1'}>{/if}

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
    {subscribe_link}
    {$myloc=serialize($__loc)}
    {foreach name=items from=$page->records item=item}
        {if $smarty.foreach.items.iteration<=$config.headcount || !$config.headcount}
        <div class="item{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
            {if $config.datetag}
                <p class="post-date">
                    <span class="month">{$item->publish_date|format_date:"%b"}</span>
                    <span class="day">{$item->publish_date|format_date:"%e"}</span>
                    <span class="year">{$item->publish_date|format_date:"%Y"}</span>
                </p>
                {$pp = ''}
            {/if}
            <{$config.item_level|default:'h2'}>
                <a href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}" title="{$item->body|summarize:"html":"para"}">
                {$item->title}
                </a>
            </{$config.item_level|default:'h2'}>
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
            {if $config.enable_facebook_like}
                <div class="fb-like" data-href="{link action=show title=$item->sef_url}" data-width="{$config.fblwidth}" data-layout="{$config.fblayout|default:'standard'}" data-action="{$config.fbverb|default:'like'}" data-size="{$config.fblsize|default:'small'}" data-share="true"></div>
            {/if}
            {if $config.enable_tweet}
                <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="{$item->title}" data-url="{link action=show title=$item->sef_url}"{if $config.twsize} data-size="{$config.twsize}"{/if} data-show-count="false">{'Tweet'|gettext}</a>
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
