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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("File Download Settings"|gettext) module="filedownload"}
		</div>
        <h2>{"File Download Settings"|gettext}</h2>
	</div>
</div>
<blockquote>
    {'This is where you can configure the settings used by this File Download module.'|gettext}&#160;&#160;
    {'These settings only apply to this particular module.'|gettext}
</blockquote>
{control type=dropdown name=order label="Sort By"|gettext items="Date Added, Date Added Descending, Date Updated, Date Updated Descending, Date Published, Date Published Descending, Number Downloads, Number Downloads Descending, Alphabetical, Reverse Alphabetical, Order Manually"|gettxtlist values="created_at,created_at DESC,edited_at,edited_at DESC,publish,publish DESC,downloads,downloads DESC,title,title DESC,rank" value=$config.order|default:'created_at DESC'}
{control type="radiogroup" name="usebody" label="Body Text"|gettext value=$config.usebody|default:0 items="Full,Summary,None"|gettxtlist values="0,1,2"}
{control type="checkbox" name="datetag" label="Display Item Date as Badge"|gettext value=1 checked=$config.datetag}
{control type="checkbox" name="printlink" label="Display Printer-Friendly and Export-to-PDF Links"|gettext description="Export-to-PDF feature requires optional dompdf add-on"|gettext value=1 checked=$config.printlink}
{control type="checkbox" name="quick_download" label="Quick Download"|gettext|cat:"?" value=1 checked=$config.quick_download}
{control type="checkbox" name="show_info" label="Show File Info"|gettext|cat:"?" value=1 checked=$config.show_info}
{control type="checkbox" name="show_icon" label="Show File Icon or Preview"|gettext|cat:"?" value=1 checked=$config.show_icon}
{control type="checkbox" name="show_player" label="Show Media Player"|gettext|cat:"?" value=1 checked=$config.show_player}
