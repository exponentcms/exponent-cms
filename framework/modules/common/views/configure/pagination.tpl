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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Pagination Settings"|gettext) module="pagination"}
		</div>
        <h2>{"Pagination Settings"|gettext}</h2>
	</div>
</div>
{control type=text name=limit label="Items per page (empty = 10, 0 = all)"|gettext value=$config.limit}
{control type=dropdown name=pagelinks label="Show page links"|gettext items="Top and Bottom,Top Only,Bottom Only,Disable page links"|gettxtlist values="Top and Bottom,Top Only,Bottom Only,Disable page links" value=$config.pagelinks}
{control type="checkbox" name="multipageonly" label="Disable page links until page limit is reached"|gettext value=1 checked=$config.multipageonly}
