{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("podcast feed settings"|gettext) module="rss"}
		</div>
        <h2>{'Podcast Feed Settings'|gettext}</h2>
	</div>
</div>
{control type="checkbox" name="enable_rss" label="Enable Podcasting"|gettext value=1 checked=$config.enable_rss}
{control type="text" name="feed_title" label="Podcast Title"|gettext value=$config.feed_title}
{control type="textarea" name="feed_desc" label="Podcast Summary"|gettext value=$config.feed_desc}
<p><strong>{'iTunes categories are semi-colon separated, with sub-categories colon separated.'|gettext}</strong>
{br}{'Only the first category and first subcategory are used.'|gettext} e.g., Category1:sub1Cat1:sub2Cat1;Category2:sub1Cat2
{control type="textarea" name="itunes_cats" label="iTunes Category"|gettext value=$config.itunes_cats}</p>
{control type="text" name="rss_limit" label="Maximum number of items to publish (0 = all)"|gettext value=$config.rss_limit|default:24 size=5}
{control type="text" name="rss_cachetime" label="Recommended feed update interval in minutes (1440 = 1 day)"|gettext value=$config.rss_cachetime|default:1440 size=5}
