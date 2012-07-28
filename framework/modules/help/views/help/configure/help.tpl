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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("help settings"|gettext) module="help"}
		</div>
        <h2>{"Help Settings"|gettext}</h2>
	</div>
</div>
{control type=dropdown name=order label="Sort By"|gettext items="Title, Order Manually"|gettxtlist values="title,rank" value=$config.order|default:rank}
{*control type="radiogroup" name="usebody" label="Body Text"|gettext value=$config.usebody|default:0 items="Full,Summary,None" values="No Float,Left,Right"  values="0,1,2"*}

