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

{css unique="twitter-config"}
{literal}
    .control.key input.text {
        width:100%;
    }
{/literal}
{/css}

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Twitter Settings"|gettext) module="twitter"}
		</div>
        <h2>{"Twitter Settings"|gettext}</h2>
	</div>
</div>
<blockquote>
    {'Log in to the Twitter Developer\'s'|gettext} <a href="https://dev.twitter.com/apps" target="_blank">{'website'|gettext}</a> {'with your twitter account.'|gettext}{br}
    {'First create an application which will provide you the Consumer key and secret.'|gettext}{br}
    {'Then you must create an Access token which will give you the Access token settings.'|gettext}{br}
    <strong>{'Give your application \'read\' & \'write\' access before requesting a token to also create tweets'|gettext}</strong>
</blockquote>
{control type="text" name="consumer_key" label="Consumer key"|gettext value=$config.consumer_key class=title}
{control type="text" name="consumer_secret" label="Consumer secret"|gettext value=$config.consumer_secret class=title}
{control type="text" name="oauth_token" label="Access token"|gettext value=$config.oauth_token class=title}
{control type="text" name="oauth_token_secret" label="Access token secret"|gettext value=$config.oauth_token_secret class=title}
{control type="text" name="twlimit" label="Number of tweets to show"|gettext size=3 filter=integer value=$config.twlimit|default:20}
{*{control type="radiogroup" name="typestatus" label="Pull Tweets from:"|gettext value=$config.typestatus|default:0 items="Home,User,Friends,Mentions,Public"|gettxtlist values="0,1,2,3,4"}*}
{control type="radiogroup" name="typestatus" label="Pull Tweets from:"|gettext value=$config.typestatus|default:0 items="Home,User,Mentions,Retweets of Me"|gettxtlist values="0,1,3,5"}
{control type=checkbox name="showattrib" value=1 label="Show attribution"|gettext|cat:"?" checked=$config.showattrib|default:0}
{control type=checkbox name="showimage" value=1 label="Show user image"|gettext|cat:"?" checked=$config.showimage|default:0}

{group label='Twitter Follow Button'|gettext}
    {control type="checkbox" name="enable_follow" label="Enable Follow Button"|gettext value=1 checked=$config.enable_follow description='Displays the \'Follow\' button'|gettext}
    {control type="text" name="twitter_user" label="Twitter User Name"|gettext value=$config.twitter_user}
    {control type=checkbox name="hideuser" value=1 label="Hide User Name in Button"|gettext checked=$config.hideuser|default:0}
    {control type="dropdown" name="size" items="Medium,Large"|gettxtlist values=",large" label="Follow Button Size"|gettext value=$config.size|default:""}
{/group}