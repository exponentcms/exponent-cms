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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("News Settings"|gettext) module="news"}
		</div>
        <h2>{"News Settings"|gettext}</h2>
	</div>
</div>
{*control type=dropdown name=order label="Sort Order"|gettext items="$sortopts" value=$config.order*}
{control type=dropdown name=order label="Sort By"|gettext items="Date Added, Date Added Descending, Date Updated, Date Updated Descending, Date Published, Date Published Descending, Rank"|gettxtlist values="created_at,created_at DESC,edited_at,edited_at DESC,publish,publish DESC,rank" value=$config.order|default:'publish DESC'}
{control type="checkbox" name="only_featured" label="Only show Featured News Items"|gettext value=1 checked=$config.only_featured}
{control type="radiogroup" name="usebody" label="Body Text"|gettext value=$config.usebody|default:0 items="Full,Summary,None"|gettxtlist values="0,1,2"}
{control type="checkbox" name="datetag" label="Display Item Date as Badge"|gettext value=1 checked=$config.datetag}
{control type="checkbox" name="printlink" label="Display Printer-Friendly and Export-to-PDF Links"|gettext description="Export-to-PDF feature requires optional dompdf add-on"|gettext value=1 checked=$config.printlink}
