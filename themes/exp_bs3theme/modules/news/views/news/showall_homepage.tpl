{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{css unique="news" link="`$asset_path`css/news.css"}

{/css}

<div class="module news showall" style="word-wrap: break-word;">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{/if}
    {if $enable_rss == true}
        <a class="rsslink" href="{rsslink}">Subscribe to {$config.feed_title}</a>
    {/if}
    {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}</h1>{/if}

    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            {icon class="add" action=edit rank=1 text="Add a news post"|gettext}
        {/if}
        {if $permissions.manage == 1}
            {if !$config.disabletags}
            |  {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
            {/if}
            {if $rank == 1}
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
    {assign var=myloc value=serialize($__loc)}
    {foreach from=$page->records item=item}
        <div class="item">
            <h2><a href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}">{$item->title}</a></h2>
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
            <span class="date">{$item->publish_date|date_format}</span>

            <div class="bodycopy">
                {if $config.filedisplay != "Downloadable Files"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                {if $config.usebody==1}
                    <p>{$item->body|summarize:"html":"parahtml"}</p>
                {elseif $config.usebody==2}
				{else}
                    {$item->body}
                {/if}
                {if $config.filedisplay == "Downloadable Files"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                <a class="biglink" href="{if $item->isRss}{$item->rss_link}{else}{link action=show title=$item->sef_url}{/if}">{"Read More"|gettext}</a>
            </div>
        </div>
        {clear}
        <img src="{$smarty.const.THEME_RELATIVE}images/dart.png" class="dart" alt="" style="max-width: 100%" />
    {/foreach}
    {if $page->total_records > $page->limit}
        {br}{pagelinks paginate=$page more=1 text="More News"|gettext}
    {/if}
</div>
