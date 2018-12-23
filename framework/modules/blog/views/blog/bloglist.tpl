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
                <p class="post-date{if $item->publish_date > $smarty.now} future-date{/if}">
                    <span class="month">{$item->publish_date|format_date:"%b"}</span>
                    <span class="day">{$item->publish_date|format_date:"%e"}</span>
                    <span class="year">{$item->publish_date|format_date:"%Y"}</span>
                </p>
            {/if}
            <{$config.item_level|default:'h2'}>
                <a href="{link action=show title=$item->sef_url}" title="{$item->body|summarize:"html":"para"}">
                {$item->title}
            </a>
            </{$config.item_level|default:'h2'}>
            <div class="post-info">
                <span class="attribution">
                    {if $item->private}<strong>({'Draft'|gettext})</strong>{/if}
                    {if !$config.datetag && $item->publish_date > $smarty.now}
                        <strong>{'Will be'|gettext}&#160;
                    {/if}
                    {$prepend = ''}
                    {if !$config.displayauthor}
                        <span class="label posted">{'Posted by'|gettext}</span>
                        <a href="{link action=showall_by_author author=$item->poster|username}" title='{"View all posts by"|gettext} {attribution user_id=$item->poster}'>{attribution user_id=$item->poster}</a>
                        {$prepend = '&#160;&#160;|&#160;&#160;'|not_bs}
                    {/if}
                    {if $config.usecategories}
                        {'in'|gettext} <a href="{link action=showall src=$item->src cat=$item->expCat[0]->id}" title='{"View all posts filed under"|gettext} {$item->expCat[0]->title}'>{if $item->expCat[0]->title!= ""}{$item->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</a>
                    {/if}
                    {if !$config.datetag}
                        {'on'|gettext} <span class="date">{$item->publish_date|format_date}</span>
                    {/if}
                    {if !$config.datetag && $item->publish_date > $smarty.now}
                        </strong>&#160;
                    {/if},
                </span>
                {comments_count record=$item prepend=$prepend}
                {$prepend = '&#160;&#160;|&#160;&#160;'|not_bs}
                {tags_assigned record=$item prepend=','|cat:$prepend}
            </div>
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
                    {/if}
                    {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                        {icon action=delete record=$item}
                    {/if}
                    {if !$item->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $item->poster == $user->id))}
                        {icon action=approve record=$item}
                    {/if}
                </div>
            {/permissions}
            <div class="bodycopy">
                {if $config.ffloat != "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                {$link = '<a href="'|cat:makeLink([controller=>blog, action=>show, title=>$item->sef_url])|cat:'"><em>'|cat:'(read more)'|gettext|cat:'</em></a>'}
    			{if $config.usebody==1}
    				{*<p>{$item->body|summarize:"html":"paralinks"}</p>*}
                    <p>{$item->body|summarize:"html":"parahtml":$link}</p>
                {elseif $config.usebody==3}
                    {$item->body|summarize:"html":"parapaged":$link}
    			{elseif $config.usebody==2}
    			{else}
    				{$item->body}
    			{/if}
                {if !$config.displayauthor}
                    {$item->poster|signature}
                {/if}
                {if $config.ffloat == "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
            </div>
            {if $config.enable_facebook_like}
                <div id="fb-root"></div>
                <div class="fb-like" data-href="{link action=show title=$item->sef_url}" data-send="false" data-width="{$config.fblwidth|default:'450'}" data-show-faces="{if $config.showfaces}true{else}false{/if}" data-font="{$config.font|default:''}" data-colorscheme="{$config.color_scheme|default:''}" data-action="{$config.verb|default:''}"></div>
                {script unique='facebook_src'}
                {literal}
                    (function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                {/literal}
                {/script}
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
        </div>
    {/foreach}
    {pagelinks paginate=$page bottom=1}
    {clear}
