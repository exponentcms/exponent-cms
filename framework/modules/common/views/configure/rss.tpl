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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("RSS Feed Settings"|gettext) module="rss-podcast"}
		</div>
        <h2>{'RSS Feed Settings'|gettext}</h2>
	</div>
</div>
<blockquote>
    {'These setting allow you make syndicate your content via RSS.'|gettext}&#160;&#160;
    {'To start syndicating, all you have to do is enable RSS and give this module\'s content a title and description!'|gettext}
</blockquote>
{control type="checkbox" name="enable_rss" label="Enable RSS"|gettext value=1 checked=$config.enable_rss}
{control type="checkbox" name="advertise" label="Advertise RSS"|gettext value=1 checked=$config.advertise}
{control type="text" name="feed_title" label="Feed Title"|gettext value=$config.feed_title}
{control type="text" name="feed_sef_url" label="Feed SEF URL"|gettext description="Auto-generated from title if left blank"|gettext value=$config.feed_sef_url}
{control type="textarea" name="feed_desc" label="Feed Description"|gettext value=$config.feed_desc}
{control type="text" name="rss_limit" label="Maximum number of RSS items to publish (0 = all)"|gettext value=$config.rss_limit|default:24 size=5}
{control type="text" name="rss_cachetime" label="Recommended RSS feed update interval in minutes (1440 = 1 day)"|gettext value=$config.rss_cachetime|default:1440 size=5}
