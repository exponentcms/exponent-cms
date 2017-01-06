{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
		    {help text="Get Help with"|gettext|cat:" "|cat:("Tag Settings"|gettext) module="tags"}
		</div>
        <h2>{"Tag Settings"|gettext}</h2>
	</div>
</div>
<h3>{'Dis-Allow tags'|gettext}</h3>
{control type=checkbox name=disabletags label="Disable Tags for this module" value=1 checked=$config.disabletags}
{*{showmodule module=expTag action=manage}*}
