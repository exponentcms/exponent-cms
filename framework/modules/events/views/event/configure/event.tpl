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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Calendar Settings"|gettext) module="events"}
		</div>
        <h2>{"Calendar Settings"|gettext}</h2>
	</div>
</div>
{control type="text" name="feed_title" label="Calendar Title"|gettext value=$config.feed_title}
{control type="text" name="feed_sef_url" label="Calendar SEF URL"|gettext value=$config.feed_sef_url}
{*control type=dropdown name=order label="Sort Order"|gettext items="$sortopts" value=$config.order*}
{*{control type=dropdown name=order label="Sort By"|gettext items="Date Added, Date Added Descending, Date Updated, Date Updated Descending, Date Published, Date Published Descending, Rank"|gettxtlist values="created_at,created_at DESC,edited_at,edited_at DESC,publish,publish DESC,rank" value=$config.order|default:'publish DESC'}*}
{control type="checkbox" name="only_featured" label="Only show Featured Events"|gettext value=1 checked=$config.only_featured}
{control type="checkbox" name="printlink" label="Display Printer-Friendly and Export-to-PDF Links"|gettext value=1 checked=$config.printlink}
{control type="checkbox" name="enable_feedback" label="Enable Feedback"|gettext value=1 checked=$config.enable_feedback}
{control type="checkbox" name="aggregate_registrations" label="Aggregate Event Registrations"|gettext value=1 checked=$config.aggregate_registrations}
