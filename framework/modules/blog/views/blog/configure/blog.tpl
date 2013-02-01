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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Blog Settings"|gettext) module="blog"}
		</div>
        <h2>{'Blog Settings'|gettext}</h2>
	</div>
</div>
<blockquote>
    {"This is where you can configure the settings used by this Blog module."|gettext}&#160;&#160;
    {"These settings only apply to this particular module."|gettext}
</blockquote>
{control type="radiogroup" name="usebody" label="Display Post Content in List"|gettext value=$config.usebody|default:0 items="Full,Summary,None"|gettxtlist values="0,1,2"}
{control type="checkbox" name="displayauthor" label="Hide author info"|gettext value=1 checked=$config.displayauthor}
{control type="checkbox" name="datetag" label="Display Item Date as Badge"|gettext value=1 checked=$config.datetag}
{control type="checkbox" name="printlink" label="Display Printer-Friendly and Export-to-PDF Links"|gettext description="Export-to-PDF feature requires optional dompdf add-on"|gettext value=1 checked=$config.printlink}
