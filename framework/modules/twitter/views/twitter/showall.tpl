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

{css unique="tweets"  link="`$asset_path`css/tweets.css"}

{/css}

<div class="module twitter showall">
	{if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
	{permissions}
	    <div class="module-actions">
	        {if $permissions.create}
	            {icon class=add action=edit text="Add a Tweet"|gettext}
	        {/if}
	    </div>
	{/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {if empty($config.consumer_key) && $permissions.configure}
        {permissions}
            <div class="module-actions">
                <div class="msg-queue notice">
                    <p>{'You MUST configure this module to use it!'|gettext} {icon action="configure"}</p>
                </div>
            </div>
        {/permissions}
    {/if}
    {if $config.enable_follow && $config.twitter_user}
        <a href="https://twitter.com/{$config.twitter_user}" class="twitter-follow-button" data-show-count="false" data-show-screen-name="{if $config.hideuser}false{else}true{/if}" data-lang="en">{'Follow'|gettext} @{$config.twitter_user}</a>
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
	<dl>
		{foreach from=$items item=item}
            {if !empty($item.retweet)}
                <div class="context">
                    <div class="tweet-context">
                        <span class="tweet-content-icon">{if bs()}{icon img='retweet.png' size=small color=green}{else}{icon img='retweet_on.png' size=small color=green}{/if}</span>
                        <span><a href="https://twitter.com/{$item.user.screen_name}">{$item.user.name}</a> {'retweeted'|gettext}</span>
                    </div>
                </div>
            {/if}
            <div class="item">
                <div class="tweet-content">
                    {if $config.showimage}
                        <div class="tweet-image">
                            {img src="`$item.image`" class="img-rounded" alt="tweet profile image"|gettext}
                            {*{if $item.retweetedbyme}{img src="`$smarty.const.PATH_RELATIVE`framework/modules/twitter/assets/images/tweeted.png" style="position:relative;top:-16px;left:-58px;"}{/if}*}
                        </div>
                    {*{elseif $item.retweetedbyme}*}
                        {*{img src="`$smarty.const.PATH_RELATIVE`framework/modules/twitter/assets/images/tweeted.png" style="float:left; margin:2px 5px;"}*}
                    {/if}
                    <dt><em class="date">{$item.created_at|relative_date}{if $config.showattrib}{if !empty($item.via)} {'via'|gettext} {$item.via},{/if} <a href="https://twitter.com/{$item.screen_name}">@{$item.screen_name}</a> {'wrote'|gettext}:{/if}</em></dt>
                    <dd>
                        {$item.text}
                        {permissions}
                            <div class="item-actions">
                                {if $permissions.create && !$item.ours && !$item.retweetedbyme}
                                    &#160;{icon img='retweet.png' id=$item.id action=create_retweet title="Retweet"|gettext onclick="return confirm('"|cat:("Are you sure you want to retweet this item?"|gettext)|cat:"');"}
                                {/if}
                                {if $permissions.delete && $item.ours && !$item.retweeted_status}
                                    &#160;{icon class=delete id=$item.id action=delete_tweet}
                                {/if}
                                {if $item.retweetedbyme}
                                    <div class="tweet-context2">
                                        <span class="tweet-content-icon">{if bs()}{icon img='retweet.png' size=small color=green}{else}{icon img='retweet_on.png' size=small color=green}{/if}</span>
                                        <span>{'Retweeted by Me'|gettext}</span>
                                    </div>
                                {/if}
                            </div>
                        {/permissions}
                    </dd>
                </div>
			</div>
			{clear}
		{/foreach}
	</dl>
</div>
