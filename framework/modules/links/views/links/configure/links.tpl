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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Links Settings"|gettext) module="links"}
		</div>
        <h2>{"Links Settings"|gettext}</h2>
	</div>
</div>
<blockquote>
    {'This is where you can configure the settings to be used by this Link Manager module.'|gettext}&#160;&#160;
    {'These settings will only apply to this particular module.'|gettext}
</blockquote>
{control type=dropdown name=order label="Sort By"|gettext items="Alphabetical, Reverse Alphabetical, Order Manually, Random"|gettxtlist values="title,title DESC,rank,RAND()" value=$config.order|default:rank}
{control type="checkbox" name="opennewwindow" label="Default to Open Link in New Window?"|gettext value=1 checked=$config.opennewwindow}
