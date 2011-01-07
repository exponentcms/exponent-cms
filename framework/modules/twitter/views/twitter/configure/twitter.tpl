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
 * @copyright  2004-2009 OIC Group, Inc.
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
{control type="text" name="consumer_key" label="Consumer key" value=$config.consumer_key class=title}
{control type="text" name="consumer_secret" label="Consumer secret" value=$config.consumer_secret class=title}
{control type="text" name="oauth_token" label="Access Token" value=$config.oauth_token class=title}
{control type="text" name="oauth_token_secret" label="Access Token Secret" value=$config.oauth_token_secret class=title}

{control type="text" name="limit" label="Number tweets to show" size=3 filter=integer value=$config.limit|default:20}
