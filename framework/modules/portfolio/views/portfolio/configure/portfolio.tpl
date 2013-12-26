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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Portfolio Settings"|gettext) module="portfolio"}
		</div>
        <h2>{"Portfolio Settings"|gettext}</h2>
	</div>
</div>
<blockquote>
    {"This is where you can configure the settings used by this Portfolio module."|gettext}&#160;&#160;
    {"These settings only apply to this particular module."|gettext}
</blockquote>
{control type=dropdown name=order label="Sort By"|gettext items="Alphabetical, Reverse Alphabetical, Order Manually"|gettxtlist values="title,title DESC,rank" value=$config.order|default:rank}
{control type="checkbox" name="only_featured" label="Only show featured Portfolio Pieces"|gettext value=1 checked=$config.only_featured}
{control type="radiogroup" name="usebody" label="Display Description in List"|gettext value=$config.usebody|default:0 items="Full,Summary,Page,None"|gettxtlist values="0,1,3,2"}
{control type="checkbox" name="printlink" label="Display Printer-Friendly and Export-to-PDF Links"|gettext description="Export-to-PDF feature requires optional pdf engine add-on"|gettext value=1 checked=$config.printlink}
