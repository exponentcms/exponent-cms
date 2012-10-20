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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("iCalendar Feed Settings"|gettext) module="icalendar"}
		</div>
        <h2>{'iCalendar Feed Settings'|gettext}</h2>
	</div>
</div>
<blockquote>
    {'These setting allow you make syndicate your calendar via iCal.'|gettext}&#160;&#160;
    {'To start syndicating, all you have to do is enable iCal and give this calendar a title and description!'|gettext}
</blockquote>
{control type="checkbox" name="enable_ical" label="Enable iCal"|gettext value=1 checked=$config.enable_ical}
{*{control type="checkbox" name="advertise" label="Advertise RSS"|gettext value=1 checked=$config.advertise}*}
{*{control type="textarea" name="feed_desc" label="iCal Feed Description"|gettext value=$config.feed_desc}*}
{control type="text" name="rss_limit" label="Maximum days of iCal items to publish (0 = all)"|gettext value=$config.rss_limit|default:24 size=5}
{control type="text" name="rss_cachetime" label="Recommended iCal feed update interval in minutes (1440 = 1 day)"|gettext value=$config.rss_cachetime|default:1440 size=5}
