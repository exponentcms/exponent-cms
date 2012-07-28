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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("youtube settings"|gettext) module="youtube"}
		</div>
        <h2>{"Youtube Settings"|gettext}</h2>
	</div>
</div>
{control type="text" name="width" label="Width of Video"|gettext size=4 filter=integer value=$config.width}
{control type="text" name="height" label="Height of Video"|gettext size=4 filter=integer value=$config.height}

