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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("MOTD Settings"|gettext) module="motd"}
		</div>
        <h2>{"MOTD Settings"|gettext}</h2>
	</div>
</div>
<p>
    {'If no Message of the Day if found for the current day we can pull up a random previous Message of the Day.'|gettext}&#160;&#160;
    {'If you would like to use this functionality check the box below.'|gettext}
</p>
{control type="checkbox" name="userand" label="Use Random MOTD"|gettext value=1 checked=$config.userand}
