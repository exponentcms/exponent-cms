{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Podcast Feed Settings"|gettext) module="rss-podcast"}
		</div>
        <h2>{'Podcast Feed Settings'|gettext}</h2>
        <blockquote>
            {'These settings allow you to syndicate your content via a Podcast.'|gettext}&#160;&#160;
            {'To start syndicating, all you have to do is enable Podcasting and give this module\'s content a title and description!'|gettext}
        </blockquote>
	</div>
</div>
{control type="checkbox" name="enable_rss" label="Enable Podcasting"|gettext value=1 checked=$config.enable_rss}
{control type="checkbox" name="advertise" label="Advertise RSS"|gettext value=1 checked=$config.advertise}
{control type="text" name="feed_title" label="Podcast Title"|gettext value=$config.feed_title}
{control type="text" name="feed_sef_url" label="SEF URL"|gettext description="Auto-generated from title if left blank"|gettext value=$config.feed_sef_url}
{control type="textarea" name="feed_desc" label="Podcast Summary"|gettext value=$config.feed_desc}
{control type="text" name="feed_artist" label="Podcast Artist"|gettext value=$config.feed_artist}
{control type="files" name="album" subtype=album label="Podcast Image"|gettext value=$config.expFile limit="1" folder=$config.upload_folder}
<p><strong>{'iTunes categories are semi-colon separated, with sub-categories colon separated.'|gettext}</strong>
{br}{'Only the first category and first subcategory are used.'|gettext} e.g., Category1:sub1Cat1:sub2Cat1;Category2:sub1Cat2
{control type="textarea" name="itunes_cats" label="iTunes Category"|gettext value=$config.itunes_cats}</p>
{control type="text" name="rss_limit" label="Maximum number of items to publish (0 = all)"|gettext value=$config.rss_limit|default:24 size=5}
{control type="text" name="rss_cachetime" label="Recommended feed update interval in minutes (1440 = 1 day)"|gettext value=$config.rss_cachetime|default:1440 size=5}
