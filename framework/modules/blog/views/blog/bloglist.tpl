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

    {$myloc=serialize($__loc)}
    {pagelinks paginate=$page top=1}
    {foreach from=$page->records item=item}
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
                    {$prepend = ''}
                    {if !$config.displayauthor}
                        <span class="label posted">{'Posted by'|gettext}</span>
                        <a href="{link action=showall_by_author author=$item->poster|username}" title='{"View all posts by"|gettext} {attribution user_id=$record->poster}'>{attribution user_id=$item->poster}</a>
                        {$prepend = '&#160;&#160;|&#160;&#160;'}
                    {/if}
                    {if $config.usecategories}
                        {'in'|gettext} <a href="{link action=showall src=$item->src cat=$item->expCat[0]->id}" title='{"View all posts filed under"|gettext} {$item->expCat[0]->title}'>{if $item->expCat[0]->title!= ""}{$item->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</a>
                    {/if}
                    {if !$config.datetag}
                        {'on'|gettext} <span class="date">{$item->publish_date|format_date}</span>
                    {/if}
                    {if $item->publish_date > $smarty.now}
                        </strong>&#160;
                    {/if}
                </span>
                {comments_count record=$item prepend=$prepend}
                {tags_assigned record=$item prepend='&#160;&#160;|&#160;&#160;'}
            </div>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
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
                </div>
            {/permissions}
            <div class="bodycopy">
                {if $config.ffloat != "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
    			{if $config.usebody==1}
    				{*<p>{$item->body|summarize:"html":"paralinks"}</p>*}
                    <p>{$item->body|summarize:"html":"parahtml"}</p>
                {elseif $config.usebody==3}
                    {$item->body|summarize:"html":"parapaged"}
    			{elseif $config.usebody==2}
    			{else}
    				{$item->body}
    			{/if}			
                {if $config.ffloat == "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
            </div>
            {if $config.enable_facebook_like}
                <div id="fb-root"></div>
                <div class="fb-like" data-href="{link action=show title=$item->sef_url}" data-send="false" data-width="{$config.width|default:'450'}" data-show-faces="{if $config.showfaces}true{else}false{/if}" data-font="{$config.font|default:''}" data-colorscheme="{$config.color_scheme|default:''}" data-action="{$config.verb|default:''}"></div>
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
