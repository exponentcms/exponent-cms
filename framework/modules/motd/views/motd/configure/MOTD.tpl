{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
		    {help text="Get Help with"|gettext|cat:" "|cat:("MOTD Settings"|gettext) module="motd"}
		</div>
        <h2>{"MOTD Settings"|gettext}</h2>
        <blockquote>
            {'If no Message of the Day is found for the current day, we can pull up a random Message of the Day.'|gettext}&#160;&#160;
        </blockquote>
	</div>
</div>
{control type="checkbox" name="userand" label="Use Random MOTD"|gettext value=1 checked=$config.userand description='Check this box to select random messages.'|gettext focus=1}
{control type="checkbox" name="datetag" label="Display Item Date as Badge"|gettext value=1 checked=$config.datetag}
