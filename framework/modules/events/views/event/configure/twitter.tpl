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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Twitter Settings"|gettext) module="tweet-button"}
		</div>
        <h2>{'Twitter Settings'|gettext}</h2>
	</div>
</div>
{group label='Auto Tweeting'|gettext}
    {control type="checkbox" name="enable_auto_tweet" label="Enable Auto-Tweet"|gettext value=1 checked=$config.enable_auto_tweet description='Allows \'Tweet\'ing new items'|gettext}
    {control type="text" name="tweet_prefix" label="Tweet prefix text"|gettext value=$config.tweet_prefix class=title description='Begin Tweet with this text'|gettext}
    {group label='Twitter Account'|gettext}
        <blockquote>
            {'Log in to the Twitter Developer\'s'|gettext} <a href="https://dev.twitter.com/apps" target="_blank">{'website'|gettext}</a> {'with your Twitter account.'|gettext}{br}
            {'First create an application which will provide you the Consumer key and secret.'|gettext}{br}
            {'Then you must create an Access token which will give you the Access token settings.'|gettext}{br}
            <strong>{'Give your application \'read\' & \'write\' access before requesting a token to create tweets'|gettext}</strong>
        </blockquote>
        {control type="text" name="consumer_key" label="Consumer key"|gettext value=$config.consumer_key class=title}
        {control type="text" name="consumer_secret" label="Consumer secret"|gettext value=$config.consumer_secret class=title}
        {control type="text" name="oauth_token" label="Access token"|gettext value=$config.oauth_token class=title}
        {control type="text" name="oauth_token_secret" label="Access token secret"|gettext value=$config.oauth_token_secret class=title}
    {/group}
{/group}
