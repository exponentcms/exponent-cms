{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Countdown Settings"|gettext) module="countdown"}
		</div>
        <h2>{'Countdown Settings'|gettext}</h2>
	</div>
</div>
<blockquote>
    {"This is where you configure the settings used by this Countdown module."|gettext}&#160;&#160;
    {"These settings only apply to this particular module."|gettext}
</blockquote>
{control type="text" name="title" label="Title"|gettext value=$config.title}
{control type="calendar" name="count" label="Countdown to Date"|gettext value=$config.count default_date=$config['date-count'] default_hour=$config['time-h-count'] default_min=$config['time-m-count'] default_ampm=$config['ampm-count']}
{control type="editor" name="message" label="Countdown Completed Message"|gettext value=$config.message}
{control type="editor" name="body" label="Message below clock"|gettext value=$config.body}
{control type="checkbox" name="light" label="Use light background for digits"|gettext description="Makes clock discernable on a theme with a dark background"|gettext value=1 checked=$config.light}
{control type="checkbox" name="displaytext" label="Display remaining time text below clock"|gettext value=1 checked=$config.displaytext}
{control type="checkbox" name="displaydate" label="Display countdown to date below clock"|gettext value=1 checked=$config.displaydate}
