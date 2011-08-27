{*
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @copyright  2004-2011 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 *}

{css unique="twitter-config"}
{literal}
.control.key input.text {
    width:100%;
}
{/literal}
{/css}



<h2>{"Configure this Module"|gettext}</h2>
<p>Log in to the Twitter Developer's website with your twitter account <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>{br}
Create an application which will give you the Consumer Key and Secret.{br}
Then you create an Access Token which will give you the Access settings.</p>
{control type="text" name="consumer_key" label="Consumer key"|gettext value=$config.consumer_key class=title}
{control type="text" name="consumer_secret" label="Consumer secret"|gettext value=$config.consumer_secret class=title}
{control type="text" name="oauth_token" label="Access Token"|gettext value=$config.oauth_token class=title}
{control type="text" name="oauth_token_secret" label="Access Token Secret"|gettext value=$config.oauth_token_secret class=title}
{control type="text" name="twlimit" label="Number of tweets to show"|gettext size=3 filter=integer value=$config.twlimit|default:20}
{control type="radiogroup" name="typestatus" label="Pull Tweets from:"|gettext value=$config.typestatus|default:0 items="User,Home,Friends,Mentions,Public" values="0,1,2,3,4"}
